<template>
    <div class="container-fruid">
        <div class="overflow-hidden">
            <div class="row">
                <div class="col-md-5">
                    <div class="row">
                        <div class="map col-md-12 relative" ref="googleMap" style="float: left" />
                        <button v-if="own.is_admin && windowWidth > 800" type="button" class="btn btn-dark absolute" @click="onBack">
                            戻る
                        </button>
                        <button v-if="own.is_admin && windowWidth <= 799" type="button" class="btn btn-dark absolute1" @click="onBack">
                            戻る
                        </button>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="radio" id="one" value="岡崎橋ビル" v-model="place"/>
                            <label for="one">岡崎橋</label>
                            <input type="radio" id="two" value="本社" v-model="place" />
                            <label for="two">本社</label>
                            
                        </div>
                        <div class="col-md-8">
                            <input type="text" v-model="geo" style="width: 250px" placeholder="上記以外の場所検索はこちら" />
                            <button v-on:click="geocoder">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div>
                                <input
                                    type="checkbox"
                                    id="checkBox1"
                                    value="和食"
                                    v-model="genres"
                                />和食        
                                <input
                                    type="checkbox"
                                    id="checkBox4"
                                    value="居酒屋"
                                    v-model="genres"
                                />居酒屋
                                <!-- <input
                                    type="checkbox"
                                    id="checkBox7"
                                    value="スーパー"
                                    v-model="genres"
                                />スーパー -->
                            </div>
                            <div>
                                <input
                                    type="checkbox"
                                    id="checkBox3"
                                    value="中華"
                                    v-model="genres"
                                />中華
                                <input
                                    type="checkbox"
                                    id="checkBox5"
                                    value="ラーメン"
                                    v-model="genres"
                                />ラーメン
                            </div>
                            <div>
                                <input
                                    type="checkbox"
                                    id="checkBox2"
                                    value="洋食"
                                    v-model="genres"
                                />洋食
                                <input
                                    type="checkbox"
                                    id="checkBox6"
                                    value="弁当"
                                    v-model="genres"
                                />弁当
                            </div>
                            
                        </div>
                        <div class="col-md-8" style="float: left;">
                            <input type="text" v-model="genreText" value="" placeholder="上記以外のジャンル検索はこちら" style="width: 250px" />
                            <button id="search" v-on:click="search">
                                <i class="fas fa-search"></i>
                            </button>
                            <div>
                                <select id="log" v-model="genreText" style="width: 250px; margin-bottom: 20px;">
                                </select>   
                            </div>
                        </div>
                    </div>
                    <div>
                        <label>検索範囲</label>
                        <input type="range" min="1" max="5" step="1" v-model="sliderNum" style="margin-bottom: 30px; margin-left: 10px" /> 
                    </div>
                    <div>
                        <span v-if="sliderNum==1">半径：200m 徒歩片道：約3分</span>
                        <span v-else-if="sliderNum==2">半径：400m 徒歩片道：約5分</span>
                        <span v-else-if="sliderNum==3">半径：600m 徒歩片道：約8分</span>
                        <span v-else-if="sliderNum==4">半径：800m 徒歩片道：約10分</span>
                        <span v-else-if="sliderNum==5">半径：1km 徒歩片道：約13分</span>
                        <label for="flexSwitchCheckDefault"
                            >半径表示:</label
                        >
                        <input
                            type="checkbox"
                            id="flexSwitchCheckDefault"
                            v-model="circleMode"
                        />
                            <!-- <span v-if="circleMode"> ON</span>
                            <span v-else> OFF</span> -->
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <span class="span-header">レビュー一覧</span>
                            <router-link to="/create" class="btn btn-primary" @click="onResume(review)" style="float: right">投稿</router-link>
                            <table class="table table-sm" key="processes">
                                <thead>
                                    <tr>
                                        <th class="text-center bg-primary text-white" style="width: 30%;">投稿者</th>
                                        <th class="text-center bg-primary text-white" style="width: 30%;">店名</th>
                                        <th class="text-center bg-primary text-white" style="width: 30%;">コメント</th>
                                        <th class="text-center bg-primary text-white" style="width: 10%;">削除</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(review, index) in reviews" :key="index" class="bg-white" style="font-size: 80%;">
                                        <td class=""> {{ review.user_name }} </td>
                                        <td class="">{{ review.name }}</td>
                                        <td style="">{{ review.comment }}</td>
                                        <td>
                                            <i class="fas fa-trash-alt fa-2x red d-flex justify-content-center" v-if="own.name === review.user_name" @click="onDelete(review.id)"></i>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <pagination
                                :page="currentPage"
                                :items-per-page="itemsPerPage"
                                :max-visible-pages="maxVisiblePages"
                                :total-items="totalItems"
                                @pageChange="pageChange"
                            />
                            <loading :active.sync="isLoading" :is-full-page="fullPage"></loading>
                        </div>
                    </div>
                </div>
            </div>   
        </div>     
    </div>       
</template>

<script>
import GoogleMapsApiLoader from "google-maps-api-loader";
import Pagination from './commons/Pagination.vue';
export default {
  components: { Pagination },
    data() {
        return {
            reviews: [],
            myAPI: "AIzaSyDoT7ZAEG-K-twxebevNoXlOXY6P7v69GA",
            map: null,
            radius: 0,
            geo: "",
            circleMode: false,
            sliderNum: 3,
            genres: [],
            logs: [],
            genreText: null,
            searchRange: 200,
            optionCount: 0,
            place: "岡崎橋ビル",
            dataCount: 0,
            latarray: [],
            lngarray: [],
            restname: [],
            openshop: [],
            BOOL: [],
            eigyou: [],
            lat: 34.68286620763049,
            lng: 135.48942106828244,
            type: "",
            keyword: "",
            selected: null,
            google: null,
            own_user_name: "",
            currentPage: 0,
            itemsPerPage: 5,
            maxVisiblePages: 5,
            totalItems: 0,
            offset: 0,
            isLoading: false,
            fullPage: false,
            windowWidth: 0,
            sort: {
                key: "id", // ソートキー
                isAsc: false // 昇順ならtrue,降順ならfalse
            },
            //デザイン
            view: 'col-md-10',

            mapConfig: {
                // 地図の中心地点
                center: {
                    lat: 34.68286620763049,
                    lng: 135.48942106828244
                },
                // 拡大率
                zoom: 15,
                // 全てのUIを消す
                disableDefaultUI: true,
                // // 地図、航空写真ボタン削除
                // mapTypeControl:false,
                // // ズームボタン削除
                // zoomControl:false,
                // // ストリートビューボタン削除
                // streetViewControl:false,
                // // フルスクリーンボタン削除
                // fullscreenControl:false,
                marker: null,
                styles: [
                    {
                        // お店アイコン
                        featureType: "poi.business",
                        stylers: [{ visibility: "off" }]
                    },
                    // 学校アイコン
                    {
                        featureType: "poi.school",
                        stylers: [{ visibility: "off" }]
                    },
                    // 白い眼玉みたいなビルアイコン
                    {
                        featureType: "landscape.man_made",
                        elementType: "labels.icon",
                        stylers: [{ visibility: "off" }]
                    },
                    // 病院アイコン
                    {
                        featureType: "poi.medical",
                        stylers: [{ visibility: "off" }]
                    },
                    // 公園アイコン
                    {
                        featureType: "poi.park",
                        stylers: [{ visibility: "off" }]
                    },
                    // 役所アイコン
                    {
                        featureType: "poi.government",
                        stylers: [{ visibility: "off" }]
                    },
                    // 神社アイコン
                    {
                        featureType: "poi.place_of_worship",
                        stylers: [{ visibility: "off" }]
                    },
                    // 観光名所アイコン
                    {
                        featureType: "poi.attraction",
                        stylers: [{ visibility: "off" }]
                    },
                    // スポーツ施設アイコン(緑のやつ)
                    {
                        featureType: "poi.sports_complex",
                        stylers: [{ visibility: "off" }]
                    }
                ]
            }
        };
    },
    async mounted() {
        this.calculateWindowWidth();
        this.google = await GoogleMapsApiLoader({
            apiKey: this.myAPI
        });
        window.addEventListener('resize', this.calculateWindowWidth);
        // this.initializeMap();
        this.radius = this.sliderNum * this.searchRange;

        document.getElementById("log").disabled = true;
        document.getElementById("search").disabled = true;
        this.initializeMap();
        this.getItems();
        
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.calculateWindowWidth);
    },
    methods: {
        async main() {
            this.dataCount = 0;
            this.latarray = [];
            this.lngarray = [];
            this.restname = [];

            if (this.genres.length > 0) {
                for (let i = 0; i < this.genres.length; i++) {
                    this.keyword = this.genres[i];
                    await this.getLatLng();
                }
            } else {
                await this.getLatLng();
            }

            this.map = new this.google.maps.Map(
                this.$refs.googleMap,
                this.mapConfig
            );

            await this.initializeMap();
            await this.createMarker();
        },
        async getLatLng() {
            await axios
                .get(
                    `https://maps.googleapis.com/maps/api/place/nearbysearch/json?key=${this.myAPI}&location=${this.lat},${this.lng}&radius=${this.radius}&type=${this.type}&keyword=${this.keyword}&language=ja`, {
                        headers: {
                            'Access-Control-Allow-Origin': '*',
                            'Access-Control-Allow-Headers': '*',
                            'Access-Control-Allow-Credentials': 'true',
                            'Access-Control-Allow-Methods': 'OPTIONS,POST,GET'
                        },
                        data:{}
                    }
                )
                .then(result => {
                    this.dataCount = Object.keys(result.data.results).length;

                    for (let i = 0; i < this.dataCount; i++) {
                        this.latarray.push(
                            result.data.results[i].geometry.location.lat
                        );
                        this.lngarray.push(
                            result.data.results[i].geometry.location.lng
                        );
                        this.restname.push(result.data.results[i].name);

                        //営業時間がある場合true,ない場合falseを返す
                        if (
                            result.data.results[i].opening_hours != null &&
                            result.data.results[i].opening_hours.open_now !=
                                null
                        ) {
                            if (
                                result.data.results[i].opening_hours.open_now ==
                                true
                            ) {
                                this.eigyou[i] = "営業しています";
                            } else {
                                this.eigyou[i] = "本日の営業は終了しました";
                            }
                            //営業しているかを判断営業していない場合、不明を返す
                        } else {
                            this.eigyou[i] = "営業情報を取得できませんでした";
                        }
                    }
                    // this.dataCount += dataCount;
                })
                // .catch(error=> {
                //     console.log(error)
                // })
        },
        async initializeMap() {
            this.mapConfig.center.lat = this.lat;
            this.mapConfig.center.lng = this.lng;
            this.map = new this.google.maps.Map(
                this.$refs.googleMap,
                this.mapConfig
            );

            if (this.circleMode) {
                // 円表示
                new google.maps.Circle({
                    map: this.map,
                    center: { lat: this.lat, lng: this.lng },
                    radius: this.radius,
                    fillColor: "#7cfc00" // 塗りつぶし色
                });
            } else {
                this.map = new this.google.maps.Map(
                    this.$refs.googleMap,
                    this.mapConfig
                );
            }
        },
        // getItems: function() {
        //   this.isLoading = true
        //   const api = axios.create()
        //   axios.all([api.get('/api/review')]).then(
        //     axios.spread((res1, res2, res3, res4) => {
        //         //格納データ取得
        //       this.reviews = res1.data

        //       this.isLoading = false
        //     }),
        //   )
        // },
        async createMarker() {
            // ジャンル検索の時のマーカー
            if (this.genres.length > 0) {
                for (var i = 0; i < this.dataCount; i++) {
                    var markers = new google.maps.Marker({
                        map: this.map,
                        position: new this.google.maps.LatLng(
                            this.latarray[i],
                            this.lngarray[i]
                        ),
                        animation: google.maps.Animation.DROP //アニメーション　BOUNCE:常に跳ねる　DROP:設置する時に降ってくる
                        // 本番環境を見るために一度停止
                        // icon: {
                        //   url : 'images/orange.png',
                        // },
                    });
                    var iw = new google.maps.InfoWindow({
                        position: new this.google.maps.LatLng(
                            this.latarray[i],
                            this.lngarray[i]
                        ),
                        content: "testtest"
                    });
                    // var msg =this.restname[i]+ '<br/><a href="https://www.google.com/maps/search/?api=1&query='+this.restname[i]+'" target="_blank" >Googleマップで見る</a><br/>' + this.eigyou[i] + 　//営業しているかのメッセージを消去する場合、this.eigyou[i]を削除
                    var msg = `${this.restname[i]}<br/><a href="https://www.google.com/maps/search/?api=1&query=${this.restname[i]}"target="_blank" >Googleマップで見る</a><br/>${this.eigyou[i]}`; //営業しているかのメッセージを消去する場合、this.eigyou[i]を削除
                    // this.attachMessage(markers, msg );
                    this.attachMessage(markers, msg, this.restname[i], i);
                }
            }
            // フリー検索の時のマーカー
            else {
                for (var i = 0; i < this.dataCount; i++) {
                    var markers = new google.maps.Marker({
                        map: this.map,
                        position: new this.google.maps.LatLng(
                            this.latarray[i],
                            this.lngarray[i]
                        ),
                        animation: google.maps.Animation.DROP //アニメーション　BOUNCE:常に跳ねる　DROP:設置する時に降ってくる
                    });

                    var msg =
                        this.restname[i] +
                        '<br/><a href="https://www.google.com/maps/search/?api=1&query=' +
                        this.restname[i] +
                        '" target="_blank" >Googleマップで見る</a><br/>' +
                        this.eigyou[i]; //営業しているかのメッセージを消去する場合、this.eigyou[i]を削除
                    this.attachMessage(markers, msg);
                }
            }
        },
        pageChange(page, start, end) {
            // console.log(page, start, end);
            if (end == 1) return;
            this.currentPage = page;
            this.offset = start;
            this.getItems();
        },

        search() {
            // チェックボックスのチェックを外す
            document.getElementById("checkBox1").checked = false;
            document.getElementById("checkBox2").checked = false;
            document.getElementById("checkBox3").checked = false;
            document.getElementById("checkBox4").checked = false;
            document.getElementById("checkBox5").checked = false;
            document.getElementById("checkBox6").checked = false;
            // document.getElementById("checkBox7").checked = false;
            this.genres = [];

            this.keyword = this.genreText;
            // this.genreTextをfilterの中で使えない為
            const a = this.genreText;
            this.main();
            const select = document.getElementById("log");

            // logsに検索欄と同じものが無いか
            const result = this.logs.filter(function(value) {
                return value === a;
            });

            // 同じものが無ければ
            if (result.length === 0) {
                // 同じ文字列を追加しないようにこちらで制御
                this.logs.unshift(this.genreText);
                const option = document.createElement("option");
                option.text = this.genreText;
                option.value = this.genreText;
                // ここで要素を先頭に追加
                select.add(option, 0);
            }

            // プルダウンの項目数
            this.optionCount = select.length;

            if (select.length > 5) {
                // 0から数えて5番目の要素削除
                select.remove(5);
                // 末尾削除
                this.logs.pop();
            } else if (select.length > 0) {
                // データがあればプルダウン許可
                select.disabled = false;
            }

            // テキストボックスを空に
            this.genreText = "";
        },
        //住所から座標変換
        async geocoder() {
            //GeocodingAPIから緯度経度取得
            var latlng = await axios.get(
                `https://maps.googleapis.com/maps/api/geocode/json?address=${this.geo}&key=${this.myAPI}`, {
                headers: {
                            'Access-Control-Allow-Origin': '*',
                            'Access-Control-Allow-Headers': '*',
                            'Access-Control-Allow-Credentials': 'true',
                            'Access-Control-Allow-Methods': 'OPTIONS,POST,GET'
                        },
                data: {}
                }
            );
            this.lat = latlng.data.results[0].geometry.location.lat;
            this.lng = latlng.data.results[0].geometry.location.lng;

            this.initializeMap();
        },
        attachMessage(marker, msg, restname) {
            google.maps.event.addListener(marker, "click", function(event) {
                new google.maps.InfoWindow({
                    content:
                        msg +
                        `<br><button onclick='location.href="/create/${restname}"' value=''>Google</button>`
                }).open(marker.getMap(), marker);
            });
        },
        // getItems: function() {
        //     this.isLoading = true;
        //     const api = axios.create();
        //     axios
        //         .all([
        //             api.get("/api/review",)
        //         ])
        //         .then(
        //             axios.spread((res1, res2, res3, res4) => {
        //                 this.reviews = res1.data;

        //                 this.isLoading = false;
        //             })
        //         );
        // },
        async getItems() {
            this.isLoading = true
            const { data } = await axios.get('/api/review', {
                params: {
                offset: this.offset,
                limit: this.itemsPerPage,
                sort: this.sort,
                },
            })
            this.reviews = data.reviews
            this.totalItems = data.total_items
            this.isLoading = false
        },
        async sortBy(key) {
            this.sort.isAsc = this.sort.key === key ? !this.sort.isAsc : true;
            this.sort.key = key;
            await this.getItems();
        },
        sortedClass(key) {
            return this.sort.key === key ? `sorted ${this.sort.isAsc ? "asc" : "desc"}`: ""
        },
        onDelete: function(review_id) {
            if (!confirm("削除してもよろしいですか？")) {
                return;
            }
            let _this = this;
            axios
                .delete("/api/review/" + review_id)
                .then(function(resp) {
                    alert("削除しました。");
                    _this.$router.go(0);
                })
                .catch(function(resp) {
                    console.log(resp);
                })
                .finally(function() {
                    //
                });
        },
        onResume(review) {
            this.$router.push({
                name: "create",
                params: { ReviewId: Review.id }
            });
        },
        onBack: function() {
            // this.$router.go(-1);
            this.$router.push({name: "menu"});
        },
        handleResize: function() {
            if (window.innnerwidth <= 800) {
                this.view = true
            } else {
                this.view = false
            }
        },
        calculateWindowWidth() {
            this.windowWidth = window.innerWidth;
            console.log(this.windowWidth);
        },
    },
    watch: {
        place: async function() {
            if (this.place == "岡崎橋ビル") {
                this.lat = 34.682910319991365;
                this.lng = 135.4893996106105;
                // マップ再読み込み
                // this.initializeMap();
            } else {
                this.lat = 34.67981481380863;
                this.lng = 135.49274708362623;
                // マップ再読み込み
                // this.initializeMap();
            }

            await this.main();
        },
        sliderNum: async function() {
            this.radius = this.sliderNum * this.searchRange;
            await this.main();
        },
        genres: async function() {
            this.main();
        },
        genreText: async function() {
            document.getElementById("search").disabled = false;
        },
        circleMode: async function() {
            this.initializeMap();
            this.createMarker();
        }
    },
    computed: {
        own() {
            return this.$store.state.user;
        }
    },
    created() {
        window.addEventListener('resize', this.handleResize)
        this.handleResize()
    },
    destroyed() {
        window.removeEventListener('resize', this.handleResize)
    }
};
</script>


<style lang="scss" scoped>
// ダサいので円形表示は保留 border-radius: 250px;
.map {
    width: 500px;
    height: 500px;
    display: inline-block;
}
.red {
    color:red;
}
.relative {
    position: relative;
}
.absolute {
    position: absolute;
    right: 0%;
}
.absolute1 {
    position: absolute;
    right:5%;
}
.sort-clicable {
  cursor: pointer;
  position: relative;
}

// body {
//   font-family: "Dosis", Helvetica, Arial, sans-serif; 
//   background: #ecf0f1;
//   color: #34495e;
//   padding-top: 40px;
//   text-shadow: white 1px 1px 1px;
// }
input[type="range"] {
  -webkit-appearance: none;
  background-color: #bdc3c7;
  width: 200px;
  height: 5px;
  border-radius: 5px;
//   outline: 0;
}
input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  background-color: #007bff;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  border: 2px solid white;
  cursor: pointer;
  transition: .3s ease-in-out;
}​
  input[type="range"]::-webkit-slider-thumb:hover {
    background-color: white;
    border: 2px solid #007bff;
  }
  input[type="range"]::-webkit-slider-thumb:active {
    transform: scale(1.0);
  }



</style>
