<template>
    <!-- Wrapper -->
    <div class="container-fluid" id="wrapper">
        <!-- Header -->
        <nav class="navbar navbar-expand-lg navbar-dark cyan darken-3">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent-4"
                    aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                <ul class="navbar-nav ml-auto">
                    <li>
                        <form class="form-inline">
                            <div class="md-form my-0">
                                <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">
                            </div>
                        </form>
                    </li>
                    <li class="nav-item dropdown" v-if="isAuthenticated">
                        <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-4" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user"></i> Profile </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-info pb-0" aria-labelledby="navbarDropdownMenuLink-4">
                            <a class="dropdown-item" href="#">My account</a>
                            <a class="dropdown-item" href="#" @click="logout()">Log out</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- Header -->

        <!-- Content -->
        <div class="container-fluid" id="content">
            <div class="row">
                <div id="left" class="col-md-3 px-0">
                    <ul class="list-group">
                        <router-link to="/home"
                                     class="list-group-item list-group-item-action"
                                     active-class="active"
                                     :class="currentPage.includes('home') ? activeClass : ''">
                            <div class="md-v-line"></div><i class="fas fa-laptop mr-4 pr-3"></i>Home
                        </router-link>
                        <router-link to="/posts"
                                     class="list-group-item list-group-item-action"
                                     active-class="active"
                                     :class="currentPage.includes('posts') ? activeClass : ''">
                            <div class="md-v-line"></div><i class="fas fa-blog mr-4 pr-3"></i>Posts
                        </router-link>
                        <router-link to="/news"
                                     class="list-group-item list-group-item-action"
                                     active-class="active"
                                     :class="currentPage.includes('news') ? activeClass : ''">
                            <div class="md-v-line"></div><i class="fas fa-newspaper mr-4 pr-3"></i>News
                        </router-link>
                    </ul>
                </div>
                <div id="center" class="col-md-6"><router-view/></div>
                <div id="right" class="col-md-3 px-0"></div>
            </div>
        </div>
        <!-- Content -->

        <!-- Footer -->
        <footer class="page-footer font-small cyan darken-3">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 py-3">
                        <div class="mb-5 flex-center">
                            <a class="fb-ic">
                                <i class="fab fa-facebook-f fa-lg white-text mr-md-5 mr-3 fa-2x"> </i>
                            </a>
                            <a class="tw-ic">
                                <i class="fab fa-twitter fa-lg white-text mr-md-5 mr-3 fa-2x"> </i>
                            </a>
                            <a class="gplus-ic">
                                <i class="fab fa-google-plus-g fa-lg white-text mr-md-5 mr-3 fa-2x"> </i>
                            </a>
                            <a class="li-ic">
                                <i class="fab fa-linkedin-in fa-lg white-text mr-md-5 mr-3 fa-2x"> </i>
                            </a>
                            <a class="ins-ic">
                                <i class="fab fa-instagram fa-lg white-text mr-md-5 mr-3 fa-2x"> </i>
                            </a>
                            <a class="pin-ic">
                                <i class="fab fa-pinterest fa-lg white-text fa-2x"> </i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Copyright -->
            <div class="footer-copyright text-center py-3">© 2020 Copyright:
                <a href="https://mdbootstrap.com/education/bootstrap/"> MDBootstrap.com</a>
            </div>
            <!-- Copyright -->
        </footer>
        <!-- Footer -->
    </div>
    <!-- Wrapper -->
</template>

<script>
    import axios from "axios";

    export default {
        name: "App",
        data: function () {
            return {
                UserName: "",
                activeClass: "active"
            };
        },
        computed: {
            currentPage() {
                return this.$route.fullPath;
            },
            isAuthenticated() {
                return this.$store.getters["security/isAuthenticated"]
            },
        },
        methods:{
            logout:function() {
                //response=> this.$router.go(response.config.url)
                this.$store.dispatch("security/logout").then(responce => {
                    if(responce.status ===  200) this.$router.push("/login");
                });
            },
        },
        created() {
            let isAuthenticated = JSON.parse(this.$parent.$el.attributes["data-is-authenticated"].value),
                user = JSON.parse(this.$parent.$el.attributes["data-user"].value);
            let payload = { isAuthenticated: isAuthenticated, user: user };
            this.$store.dispatch("security/onRefresh", payload);
            axios.interceptors.response.use(undefined, err => {
                return new Promise(() => {
                    if (err.response.status === 401) {
                        this.$router.push({path: "/login"})
                    } else if (err.response.status === 500) {
                        document.open();
                        document.write(err.response.data);
                        document.close();
                    }
                    throw err;
                });
            });
        },
    }
</script>
<style lang="sass">
    @import './sass/main.sass';
</style>