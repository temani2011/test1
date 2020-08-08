<template>
    <div>
        <div v-if="isLoading" class="row col">
            <p>Loading...</p>
        </div>
        <div v-else>
            <div class="row mb-3" v-if="canCreatePost">
                <div class="col">
                    <router-link  :to="'/user/new'" tag="button" class="btn btn-md btn-primary btn-block m-0">
                        Создать нового пользователя
                    </router-link>
                </div>
                <div class="col">
                    <router-link :to="'/user/' + id + '/edit'" tag="button" class="btn btn-md btn-primary btn-block m-0">
                        Редактировать текущего пользователя
                    </router-link>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <div class="card shadow-light">
                        <div class="card-header white mb-1 pt-3half pb-0 border-0 text-dark">
                            <h4 class="card-title mb-0" style="font-weight: 500">
                                <span> Данные пользователя {{ login }} </span>
                            </h4>
                        </div>
                        <hr style="border-top-width: 2px"/>
                        <div class="card-body">
                            <div class="row">
                                <div class="col"> id </div>
                                <div class="col mr-auto"> {{ id }} </div>
                            </div>
                            <div class="row">
                                <div class="col"> login </div>
                                <div class="col mr-auto"> {{ login }} </div>
                            </div>
                            <div class="row">
                                <div class="col"> roles </div>
                                <div class="col mr-auto"> {{ roles[0] }} </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import ErrorMessage from "../components/ErrorMessage";
    export default {
        name: "User",
        components: {
            ErrorMessage
        },
        data() {
            return {
                id: "",
                password: "",
                login: "",
                roles: []
            }
        },
        created() {
            this.$store.dispatch("user/getUserById", this.$route.params.id).then(response => {
                this.id = response.id;
                this.login = response.login;
                this.roles = response.roles;
            });
        },
        computed: {
            isLoading() {
                return this.$store.getters["user/isLoading"];
            },
            hasError() {
                return this.$store.getters["user/hasError"];
            },
            getError() {
                return this.$store.getters["user/getError"];
            },
            hasUsers() {
                return this.$store.getters["user/hasUsers"];
            },
            getUsers() {
                return this.$store.getters["user/getUsers"];
            },
            canCreatePost() {
                return this.$store.getters["security/hasRole"]("ROLE_FOO");
            }
        },
        methods: {
            /*
            async DeleteNews(event){
                console.log(event.currentTarget.id);
                if (confirm('Are you sure you want to delete this news?')){
                    await this.$store.dispatch("news/deleteNews", event.currentTarget.id);
                }
            },
            async createPost() {
                const result = await this.$store.dispatch("post/create", this.$data.message);
                if (result !== null) {
                    this.$data.message = "";
                }
            }
                    */
        }
    }
</script>