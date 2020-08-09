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
                        Редактировать данные пользователя
                    </router-link>
                </div>
                <div class="col">
                    <button :id="id" @click="DeleteUser($event)" class="btn btn-md btn-primary btn-block m-0">
                        Удалить пользователя
                    </button>
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
    import ErrorMessage from "../ErrorMessage";
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
            if(this.$store.getters["user/hasUsers"]) {
                let user = this.$store.getters["user/getUsers"];
                user = user.find(x => x.id == this.$route.params.id);
                console.log(user);
                if(typeof user !== 'undefined') {
                    this.id = user.id;
                    this.login = user.login;
                    this.roles = user.roles;
                }
            } else this.$store.dispatch("user/getUserById", this.$route.params.id).then(response => {
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
            async DeleteUser(event){
                console.log(event.currentTarget.id);
                event.preventDefault();
                if (confirm('Are you sure you want to delete this news?')){
                    await this.$store.dispatch("user/deleteUser", event.currentTarget.id);
                    console.log('suc');
                }
            },
        }
    }
</script>