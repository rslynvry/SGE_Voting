<template>
    <div class="bg-img">
        <div class="login container frost">
            
            <!--Change to Election Management when in EMS-->
            <div class="header" style="display: flex; flex-direction: column;">
                <div class="logo">
                    <img src="../../images/puplogo.png" width="88px" height="88px" alt="logo">
                </div>
                <div class="div" style="display: flex; align-items: center;">
                    <h1 class="my-3">Voting System</h1>
                    <span class="beta">βeta</span>
                </div>
            </div>

            <p class="cast">Sign in to cast your vote</p>

            <div class="alert alert-danger" style="text-align: center; margin-top: 3%;" role="alert" v-if="invalid">
                {{ invalid }}
            </div>

            <div class="input mb-4 mx-4">
                    <div class="form-group">
                        <label class="form-label" for="student-number">Student Number</label>
                        <input class="form-control" type="text" id="student-number" placeholder="Enter your student number" name="student-number" maxlength="15" @keyup.enter="submitForm" v-model="form.StudentNumber">
                        <div class="invalid-feedback">
                            Please enter your student number.
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input class="form-control" type="password" id="password" name="password" placeholder="Enter your password" @keyup.enter="submitForm" v-model="form.Password">
                        <div class="invalid-feedback">
                            Please enter your password.
                        </div>
                    </div>

                    <div class="submit">
                        <button class="signin" @click="submitForm">{{ login_text }}</button>
                    </div>
            </div>
            
            <p class="terms mx-4">By using this service, you understood and agree to the PUP Online Services <a href="https://www.pup.edu.ph/terms/" class="redirect">Terms of Use</a> and <a href="https://www.pup.edu.ph/privacy/" class="redirect">Privacy Statement</a></p>
        </div>
    </div>

    <title>Login - Voting System</title>
</template>

<script>
    import axios from 'axios';
    import Body from '../Shared/Body.vue';
    import { router } from '@inertiajs/vue3';

    export default {
        components: { Body },
        data() {
            return {
                form: { StudentNumber: '',
                        Password: '',
                        student_id: 0
                        },
                loggingIn: false,
                invalid: '',
                login_text: 'Sign in',
                countdown: 0,
                intervalId: null,
            }
        },
        mounted() {
            // Commented out 

            /*if (this.$page.props.flash.token_invalid) {
                this.invalid = this.$page.props.flash.token_invalid;
            }*/
        },
        created() {
            // Clear the local storage anything name starts from votes (votes storage/cache basically)
            // To prevent when token is expired, so the votes will be cleared

            for (const [key, value] of Object.entries(localStorage)) {
                if (key.startsWith('votes')) {
                    localStorage.removeItem(key);
                }
            }
        },
        methods: {
             submitForm() {
                if (this.countdown > 0) {
                    return;
                }
                if (this.loggingIn) {
                    return;
                }
                if (this.form.StudentNumber === '') {
                    this.invalid = 'Please enter your student number.';
                    return;
                }
                if (this.form.Password === '') {
                    this.invalid = 'Please enter your password.';
                    return;
                }

                this.loggingIn = true;
                this.login_text = 'Signing in...';

                    axios.post(`${import.meta.env.VITE_FASTAPI_BASE_URL}/api/v1/student/voting/login`, {
                        StudentNumber: this.form.StudentNumber,
                        Password: this.form.Password
                    })
                    .then(response => {
                        if (response.data.message === true) {
                            this.student_id = response.data.student_id;

                            axios.post('/login/auth', this.form)
                            .then(response => {
                                if (response.data.redirect) {
                                    router.visit('/home', {
                                        data: {
                                            student_number: this.form.StudentNumber,
                                            student_id: this.student_id,
                                        }
                                    })
                                    //window.location.href = response.data.redirect;
                                }
                            })
                            .catch(error => {
                                console.log(error)

                                if (error.response.data.message === 'CSRF token mismatch.') {
                                    this.invalid = 'Please refresh your page and try again.';
                                    this.loggingIn = false;
                                    this.login_text = 'Login';
                                    return;
                                }
                            });
                        }
                        else {
                            this.invalid = 'Invalid credentials.';
                            this.loggingIn = false;
                            this.login_text = 'Login';
                        }
                    })
                    .catch(error => {
                        console.log(error)

                        // Too many requests, via throttle middleware of Laravel
                        if (error.response) {
                            if (error.response.status === 429) { 
                                const retryAfter = error.response.headers['retry-after'];
                                this.startCountdown(retryAfter);
                            }
                            else if (error.response.status === 419) {
                                this.invalid = 'Please refresh your page and try again.';
                            }
                            else {
                                // handle other errors
                            }
                        }

                        this.loggingIn = false;
                        this.login_text = 'Login';
                    });
            },
            startCountdown(seconds) {
                this.countdown = seconds;
                this.intervalId = setInterval(() => {
                    this.countdown--;
                    if (this.countdown === 0) {
                        clearInterval(this.intervalId);
                        this.invalid = '';
                    } else {
                        this.invalid = `Too many login attempts. Please wait ${this.countdown} seconds before trying again.`;
                    }
                }, 1000);
            },
        }
    };

</script>

<style scoped>
.bg-img{
    width: 100%;
    background-image: url('../../images/pupqc.jpg'); 
    background-size: cover; 
    background-repeat: no-repeat;
    height: 100vh;
    align-items: center;
    justify-content: center;
    font-family: 'Inter', sans-serif;
}

    .login{
        background-color: #F2F2F2;
        width: 33%;
        min-height: 100vh;
        padding-top: 5.5%;
        float: right;
    }

    .logo{
        text-align: center;
    }

    .header{
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login h1{   
        text-align: center;
        font-size: 32px;
        font-weight: bold;
    }

    .cast{
        text-align: center;
    }

    .beta{
        margin-left: 10px;
        font-size: 14px;
        font-weight: 300;
    }

    .input{
        margin-top: 2%;
    }

    .form-group{
        margin-bottom: 10px;
    }

    .form-control:focus {
        border-color: #800000; /* Change this to your desired color */
        box-shadow: 0 0 0 0.2rem rgba(114, 18, 18, 0.25); /* Change this to match your desired color */
    }

    .submit{
        text-align: center;
        margin-top: 5%;
    }

    .submit button{
        width: 100%;
        height: 40px;
        border-radius: 3px;
        border: transparent;
        background-color: rgba(117, 0, 0, 0.926);
        color: white;
    }

    .submit button:hover{
        background-color: rgba(117, 0, 0, 0.979);
    }

    .terms{
        font-size: 16px;
        text-align: center;
    }

    .redirect{
        text-decoration: none;
        color: #750000;
    }

    .redirect:hover{
        color: #800000;
    }

    .frost{
        min-height: 100vh;
        min-width: 360px !important;
        padding: 10vh 0 0 0;
        overflow: hidden;
        box-shadow: none !important;
        margin: 0 !important;
        border-radius: 0 !important;
        background: rgba(255,255,255,0.7);
        -webkit-backdrop-filter: blur(20px) saturate(168%);
        backdrop-filter: blur(20px) saturate(168%);
        right: 0;
        position: absolute;
    }
</style>
