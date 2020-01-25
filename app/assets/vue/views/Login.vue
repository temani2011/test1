<template>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="header pt-3 grey lighten-2">
                            <div class="row d-flex justify-content-start">
                                <h3 class="deep-grey-text mt-3 mb-4 pb-1 mx-5">Log in</h3>
                            </div>
                        </div>
                        <div class="card-body mx-4 mt-4">
                            <!-- Login -->
                            <div class="md-form">
                                <i class="fas fa-envelope prefix"></i>
                                <input type="email"
                                       id="inputValidationEx"
                                       class="form-control validate"
                                       v-model="login">
                                <label for="inputValidationEx" data-error="wrong" data-success="right">Type your email</label>
                            </div>
                            <!-- Password -->
                            <div class="md-form">
                                <i class="fas fa-lock prefix"></i>
                                <input type="password"
                                       id="inputValidationEx2"
                                       class="form-control validate"
                                       v-model="password">
                                <label for="inputValidationEx2" data-error="wrong" data-success="right">Type your password</label>
                            </div>
                            <p class="font-small grey-text d-flex justify-content-end">Forgot <a href="#" class="dark-grey-text font-weight-bold ml-1"> Password?</a></p>
                            <div class="text-center mb-4 mt-5">
                                <button type="button"
                                        class="btn btn-success btn-block"
                                        :disabled="login.length === 0 || password.length === 0 || isLoading"
                                        @click="performLogin()"
                                        v-if="!isLoading">Log in</button>
                                <div class="spinner-border" role="status" v-else>
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <div v-if="hasError" class="row col">
                                    <error-message :error="error" />
                                </div>
                            </div>
                            <p class="font-small grey-text d-flex justify-content-center">Don't have an account? <a href="#" class="dark-grey-text font-weight-bold ml-1"> Sign up</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!--
    <div>
        <div class="row col">
            <h1>Login</h1>
        </div>

        <div class="row col">
            <form>
                <div class="form-row">
                    <div class="col-4">
                        <input
                                v-model="login"
                                type="text"
                                class="form-control"
                        >
                    </div>
                    <div class="col-4">
                        <input
                                v-model="password"
                                type="password"
                                class="form-control"
                        >
                    </div>
                    <div class="col-4">
                        <button
                                :disabled="login.length === 0 || password.length === 0 || isLoading"
                                type="button"
                                class="btn btn-primary"
                                @click="performLogin()"
                        >
                            Login
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="spinner-border" role="status" v-if="isLoading">
            <span class="sr-only">Loading...</span>
        </div>

        <div
                v-else-if="hasError"
                class="row col"
        >
            <error-message :error="error" />
        </div>
    </div>
    -->
</template>

<script>
    import ErrorMessage from "../components/ErrorMessage";
    export default {
        name: "Login",
        components: {
            ErrorMessage,
        },
        data() {
            return {
                login: "",
                password: ""
            };
        },
        computed: {
            isLoading() {
                return this.$store.getters["security/isLoading"];
            },
            hasError() {
                return this.$store.getters["security/hasError"];
            },
            error() {
                return this.$store.getters["security/error"];
            }
        },
        created() {
            let redirect = this.$route.query.redirect;
            if (this.$store.getters["security/isAuthenticated"]) {
                if (typeof redirect !== "undefined") {
                    this.$router.push({path: redirect});
                } else {
                    this.$router.push({path: "/home"});
                }
            }
        },
        methods: {
            async performLogin() {
                let payload = {login: this.$data.login, password: this.$data.password},
                    redirect = this.$route.query.redirect;
                await this.$store.dispatch("security/login", payload);
                if (!this.$store.getters["security/hasError"]) {
                    if (typeof redirect !== "undefined") {
                        this.$router.push({path: redirect});
                    } else {
                        this.$router.push({path: "/home"});
                    }
                }
            }
        }
    }
</script>