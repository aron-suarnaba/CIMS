<script setup>
import { Link, router } from '@inertiajs/vue3';
import avatarPath from '/public/img/avatar.png';
import { useDateFormatter } from '@/composables/useDateFormatter';

const { formatDate } = useDateFormatter();

const toggleSidebar = () => {
    const body = document.body;
    if (body.classList.contains('sidebar-collapse')) {
        body.classList.remove('sidebar-collapse');
        body.classList.add('sidebar-open');
    } else {
        body.classList.add('sidebar-collapse');
        body.classList.remove('sidebar-open');
    }
};


const handleLogout = () => {
    router.post(route('logout'));
};
</script>
<template>
    <nav
        class="app-header navbar navbar-expand bg-primary text-white"
        data-bs-theme="dark"
        id="navigation"
        tabindex="-1"
    >
        <div class="container-fluid">
            <ul class="navbar-nav" role="navigation" aria-label="Navigation 1">
                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="#"
                        role="button"
                        @click.prevent="toggleSidebar"
                    >
                        <i class="bi bi-list"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-md-block">
                    <a href="#" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-md-block">
                    <a href="#" class="nav-link">Contact</a>
                </li>
            </ul>
            <ul
                class="navbar-nav ms-auto"
                role="navigation"
                aria-label="Navigation 2"
            >
                <li class="nav-item">
                    <a
                        class="nav-link"
                        data-widget="navbar-search"
                        href="#"
                        role="button"
                    >
                        <i class="bi bi-search"></i>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" data-bs-toggle="dropdown" href="#">
                        <i class="bi bi-chat-text"></i>
                    </a>
                    <div
                        class="dropdown-menu dropdown-menu-lg dropdown-menu-end"
                    >
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer"
                            >See All Messages</a
                        >
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link" data-bs-toggle="dropdown" href="#">
                        <i class="bi bi-bell-fill"></i>
                    </a>
                    <div
                        class="dropdown-menu dropdown-menu-lg dropdown-menu-end"
                    >
                        <span class="dropdown-item dropdown-header"
                            >15 Notifications</span
                        >
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item dropdown-footer">
                            See All Notifications
                        </a>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                        <i
                            data-lte-icon="maximize"
                            class="bi bi-arrows-fullscreen"
                        ></i>
                        <i
                            data-lte-icon="minimize"
                            class="bi bi-fullscreen-exit"
                            style="display: none"
                        ></i>
                    </a>
                </li>

                <li class="nav-item dropdown user-menu">
                    <a
                        href="#"
                        class="nav-link dropdown-toggle d-flex align-items-center justify-content-center gap-2"
                        data-bs-toggle="dropdown"
                    >
                        <img
                            :src="avatarPath"
                            class="user-image rounded-circle shadow"
                            alt="User Image"
                        />
                        <span class="d-none d-md-inline"
                            >{{ $page.props.auth.user.first_name || 'Error' }}
                            {{
                                $page.props.auth.user.last_name || 'Error'
                            }}</span
                        >
                    </a>
                    <ul
                        class="dropdown-menu dropdown-menu-lg dropdown-menu-end"
                    >
                        <li
                            class="user-header text-bg-primary d-flex flex-column align-items-center justify-content-center my-auto gap-2 p-4"
                        >
                            <img
                                :src="avatarPath"
                                class="rounded-circle shadow"
                                alt="User Image"
                            />
                            <div
                                class="d-flex flex-column align-items-center justify-content-center text-center"
                            >
                                <h3 class="mb-0">
                                    {{ $page.props.auth.user.first_name || '' }}
                                    {{ $page.props.auth.user.last_name || '' }}
                                </h3>
                                <span class="text-muted fs-6 mb-2 text-wrap"
                                    >{{ $page.props.auth.user.position || '' }}
                                    at
                                    {{
                                        $page.props.auth.user.department || ''
                                    }}</span
                                >
                                <small
                                    >Member since
                                    {{
                                        formatDate(
                                            $page.props.auth.user.created_at,
                                        ) || '2015'
                                    }}</small
                                >
                            </div>
                        </li>
                        <li class="user-body">
                            <div class="row">
                                <div class="col-4 text-center">
                                    <a href="#">Followers</a>
                                </div>
                                <div class="col-4 text-center">
                                    <a href="#">Sales</a>
                                </div>
                                <div class="col-4 text-center">
                                    <a href="#">Friends</a>
                                </div>
                            </div>
                        </li>
                        <li class="user-footer">
                            <Link
                                :href="
                                    route(
                                        'user.index',
                                        $page.props.auth.user.employee_id,
                                    )
                                "
                                class="btn btn-body-tertiary btn-flat"
                                >Profile</Link
                            >
                            <button
                                type="button"
                                @click.prevent="handleLogout"
                                class="btn btn-default btn-flat float-end"
                            >
                                Sign out
                            </button>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</template>
