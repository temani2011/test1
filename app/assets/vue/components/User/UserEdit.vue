<template>
    <div>
        <div v-if="isLoading" class="row col">
            <p>Loading...</p>
        </div>
        <div v-else>
            <div class="card mb-3half shadow-light">
                <div class="card-header white mb-1 pt-3half pb-0 border-0 text-dark">
                    <h4 class="card-title mb-0" style="font-weight: 500">
                        <span> Редактирование пользователя </span>
                    </h4>
                </div>
                <hr style="border-top-width: 2px"/>
                <div class="card-body pt-0 pb-3">
                    <label>Логин</label>
                    <div class="form-group">
                        <input type="text" v-model="login" class="form-control rounded-0">
                    </div>

                    <label>Пароль</label>
                    <div class="form-group">
                        <label>Текущий</label> <input v-model="current_password" type="password" class="form-control rounded-0">
                        <label>Новый</label> <input v-model="new_password" type="password" class="form-control rounded-0">
                    </div>

                    <label>Роли</label>
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="ROLE_FOO" v-model="roles">
                            <label class="form-check-label" for="inlineCheckbox1">ROLE_FOO</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="ROLE_FOO2" v-model="roles">
                            <label class="form-check-label" for="inlineCheckbox2">ROLE_FOO2</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="ROLE_FOO3" v-model="roles">
                            <label class="form-check-label" for="inlineCheckbox2">ROLE_FOO3</label>
                        </div>
                    </div>
                    <!--<div class="form-group">
                        <select class="custom-select rounded-0" id="inputGroupSelect01" v-model="roles">
                            <option selected>Выбрать...</option>
                            <option value="ROLE_FOO">ROLE_FOO</option>
                            <option value="ROLE_FOO2">ROLE_FOO2</option>
                            <option value="ROLE_FOO3">ROLE_FOO3</option>
                        </select>
                    </div>
                    -->
                    <!--<div class="btn-group" role="group" aria-label="Basic example">
                                <label class="radio-inline">
                                    <input type="radio" value="ROLE_FOO"  name="optradio" checked>
                                    ROLE_FOO
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" value="ROLE_FOO2" name="optradio">
                                    ROLE_FOO2
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" value="ROLE_FOO3" name="optradio">
                                    ROLE_FOO3
                                </label>
                            </div>
                    -->
                    <button @click="editUser()" class="btn btn-md m-0 btn-primary">Сохранить</button>
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
                current_password: "",
                new_password: "",
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
            async editUser(event){
                console.log(this.id, this.login, this.current_password, this.new_password, this.roles);
                let p = [this.id, this.login, this.current_password, this.new_password, this.roles];
                const result = await this.$store.dispatch("user/editUser", p);
                this.$router.push({path:'/user/' + result.id});
            },
        }
    }
</script>