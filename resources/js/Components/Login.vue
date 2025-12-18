<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login.store'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>
<template>
    <div class="login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href="#"><b>CIMS</b></a>
            </div>
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">Sign in to start your session</p>
                    <div
                        v-if="form.errors.email"
                        class="alert alert-danger"
                        role="alert"
                    >
                        {{ form.errors.email }}
                    </div>
                    <form @submit.prevent="submit">
                        <div class="input-group mb-3">
                            <input
                                type="email"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.email }"
                                placeholder="Email"
                                aria-label="Email"
                                name="email"
                                v-model="form.email"
                                autofocus
                                required
                            />
                            <div class="input-group-text">
                                <span class="bi bi-envelope"></span>
                            </div>
                            <div
                                v-if="form.errors.email"
                                class="invalid-feedback"
                            >
                                {{ form.errors.email }}
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input
                                type="password"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.password }"
                                placeholder="Password"
                                aria-label="Password"
                                name="password"
                                v-model="form.password"
                                required
                            />
                            <div class="input-group-text">
                                <span class="bi bi-lock-fill"></span>
                            </div>
                            <div
                                v-if="form.errors.password"
                                class="invalid-feedback"
                            >
                                {{ form.errors.password }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="flexCheckDefault"
                                        v-model="form.remember"
                                    />
                                    <label
                                        class="form-check-label"
                                        for="flexCheckDefault"
                                    >
                                        Remember Me
                                    </label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-grid gap-2">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        :disabled="form.processing"
                                    >
                                        Sign In
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div
                        class="social-auth-links d-grid mb-3 gap-2 text-center"
                    >
                        <p>OR</p>
                        <a href="#" class="btn btn-primary">
                            <i class="bi bi-facebook me-2"></i> Sign in using
                            Facebook
                        </a>
                        <a href="#" class="btn btn-danger">
                            <i class="bi bi-google me-2"></i> Sign in using
                            Google+
                        </a>
                    </div>

                    <p class="mb-1">
                        <a href="forgot-password.html">I forgot my password</a>
                    </p>
                    <p class="mb-0">
                        <a href="register.html" class="text-center">
                            Register a new membership
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
