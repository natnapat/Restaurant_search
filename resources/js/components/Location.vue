<template>
    <div class="row">
        <div class="col-md-3">
            <!-- Search Bar-->
            <div class="pt-2 d-inline-flex w-100">
                <input 
                    class="form-control me-2" 
                    type="text" 
                    placeholder="Search" 
                    aria-label="Search"
                    v-model="searchKeyword"
                >
                <button class="btn btn-outline-primary"  @click="search">
                    <i class="bi bi-search" style="font-size: 1rem; color: black;"></i>
                </button>
            </div>
            <!-- End Search Bar-->

            <!-- Restaurant list -->
            <div class="pt-2 vh-100 overflow-auto">
                <div class="card mb-3 w-100" style="font-size: 12px;" v-for="(restaurant, index) in restaurants">
                    <div class="row g-0">
                        <div class="col-md-8">
                            <!-- Card Body -->
                            <div class="card-body" @click="cardClick(index)">
                                <h6 class="card-title">{{restaurant.name}}</h6>
                                <div class="d-inline-flex" style="font-size: 14px;">
                                    <span class="card-text" >Rating: {{restaurant.rating}}&nbsp</span>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <span class="card-text" >({{restaurant.user_ratings_total}})</span>
                                    <span class="card-text">&nbsp</span>
                                    <span class="card-text text-muted" v-for="n in restaurant.price_level">$</span>
                                </div>
                                <p class="card-text text-muted">{{restaurant.address}}</p>
                                <div v-if="restaurant.is_open == true" class="text-success card-text" style="font-size: 16px;">Open</div>
                                <div v-if="restaurant.is_open == false" class="text-danger card-text" style="font-size: 16px;">Closed</div>
                            </div>
                            <!-- End Card Body -->
                        </div>

                        <!-- Card IMG -->
                        <div class="col-md-4">
                            <img v-if="restaurant.photo_reference!=''" v-bind:src="imgBaseUrl+restaurant.photo_reference+'&key='+apiKey" class="h-100 w-auto rounded-start" alt="">
                        </div>

                    </div>
                </div>
            </div>
            <!-- End Restaurant list -->  
        </div>
        <!-- Google Map -->
        <div id="map" class="col-md-9 bg-danger vh-100" ></div>
    </div>
    
</template>

<script>
import axios from 'axios'
    export default {
        name: 'Location',
        data(){
            return{
                apiKey: "AIzaSyByNZyJmwVZiHJ7af4Y37n9C-C9iUZppVc",
                map: null,
                markers: null,
                infoWindow: null,
                mapCenter:{lat:13.8216185,lng:100.5328013},
                searchKeyword: "Bang sue",
                restaurants:[],
                locations : [],
                imgBaseUrl: "https://maps.googleapis.com/maps/api/place/photo?maxwidth=150&photo_reference=",
            }
        },
        async created() {
            // Init google map with default search result
            await this.search();
        },
        methods: {
            // Create map
            initMap() {
                this.map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: this.mapCenter.lat, lng: this.mapCenter.lng},
                zoom: 13
                });
            },

            // Place markers on map
            addMarkers(){
                this.infoWindow = new google.maps.InfoWindow({
                    content: "",
                    disableAutoPan: true,
                });

                const map = this.map;

                // Create an array of alphabetical characters used to label the markers.
                const labels = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                // Add some markers to the map.
                this.markers = this.locations.map((position,i)=>{
                    const label = labels[i % labels.length];
                    const marker = new google.maps.Marker({
                        position,
                        label,
                    });

                    // markers can only be keyboard focusable when they have click listeners
                    // open info window when marker is clicked
                    marker.addListener("click", () => {
                        this.infoWindow.setContent(this.restaurants[i].name + '(' + this.restaurants[i].address + ')');
                        this.infoWindow.open(map, marker);
                        map.setZoom(16);
                        map.setCenter(marker.getPosition());
                    });
                    return marker;
                });

                // Add a marker clusterer to manage the markers.
                const markers = this.markers;
                new markerClusterer.MarkerClusterer({ markers, map });
            },

            // Get search result and place markers
            async search() {
                this.locations = [];
                this.markers = [];

                // Get result using default keyword
                let url = 'http://localhost:80/restaurant/' + this.searchKeyword;
                await axios.get(url)
                .then((resp) => {
                    this.restaurants = resp.data;
                    this.restaurants.forEach((restaurant,i) => {
                        this.locations.push(restaurant.location);
                    });
                    this.mapCenter = this.locations[5];
                });

                this.initMap();
                this.addMarkers();
            },

            // Pop up info window and zoom to the clicked marker
            cardClick(index) {
                this.infoWindow.setContent(this.restaurants[index].name + '(' + this.restaurants[index].address + ')');
                this.infoWindow.open(this.map, this.markers[index]);
                this.map.setZoom(18);
                this.map.setCenter(this.markers[index].getPosition());
            }
            
        }
    }
</script>