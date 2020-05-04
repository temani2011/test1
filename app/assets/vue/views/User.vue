<template>
    <div>
        <div v-if="isLoading" class="row col">
            <p>Loading...</p>
        </div>
        <div v-else-if="state === 'new' || state === 'edit'">
            <!-- Material input -->
            <div class="md-form form-group mt-5">
                <input type="text" v-model="password" class="form-control" id="formGroupExampleInputMD">
                <label for="formGroupExampleInputMD">password</label>
            </div>
            <!--choose-->
            <div class="input-group mb-3">
                <select class="browser-default custom-select" id="inputGroupSelect01"  v-model="roles">
                    <option selected>Choose...</option>
                    <option  value="ROOL_FOO">ROLE_FOO</option>
                    <option value="ROOL_FOO2">ROLE_FOO2</option>
                </select>
            </div>
            <button @click="state === 'new' ? createNews() : editNews()" type="button" class="btn btn-primary">Send</button>
        </div>
        <div v-else>
            <div v-if="canCreatePost">
                <button @click="toEdit()" type="button d-block">
                    Edit New
                </button>
            </div>
            <div v-if="canCreatePost">
                <button  @click="toNew()" type="button d-block">
                    Create New
                </button>
            </div>
            <!-- Card Light -->
            <div class="card mb-3half shadow-light">
                <div class="card-body">
                    <ul class="list-unstyled list-inline m-0">
                        <li><span>id:</span>{{ id }}</li>
                        <li><span>login:</span>{{ login }}</li>
                        <li><span>role:</span>{{ roles[0] }}</li>
                    </ul>
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
                state: "select",
                id: "",
                password: "",
                login: "",
                roles: []
            }
        },
        created() {
            console.log(this.$route.params.id);
            console.log(this.$route.params.msg);
            if(this.$route.params.msg === 'new') { this.state = 'new'; return; }
            else this.$store.dispatch("user/getUserById", this.$route.params.id).then(response =>{
                this.id = response.id;
                this.login = response.login;
                this.roles = response.roles;
            });
            if(this.$route.params.msg === 'edit') this.state = 'edit';
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
            toEdit(){
                this.state = 'edit';
            },
            toNew(){
                this.state = 'new';
            }
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