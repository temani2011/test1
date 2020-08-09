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
            </div>
            <div v-for="item in users">
                <router-link :to="'/user/' + item.id">
                    <div class="card shadow-light mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto"> {{ item.id }} </div>
                                <div class="col mr-auto"> {{ item.login }} </div>
                                <div class="col-auto">
                                    <div class="btn-group" role="group" v-if="canCreatePost">
                                        <div id="btnGroupDropEdit" type="button" class="close pl-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                                        </div>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDropEdit">
                                            <router-link class="dropdown-item" :id="item.id" :to="'user/'+ item.id +'/edit'">
                                                Редактировать
                                            </router-link>
                                            <a class="dropdown-item" :id="item.id" @click="DeleteUser($event)">Удалить</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </router-link>
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
                users: []
            }
        },
        created() {
            this.$store.dispatch("user/getAllUsers").then(response => {
                this.users = response;
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