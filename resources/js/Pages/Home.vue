<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
defineOptions({ layout: HomeLayout });
</script>
<template>
    <div class="app-content-header">
        <div class="container">
            <div class="row my-4">
                <div class="col-sm-6">
                    <h1 class="h3 mb-0">License</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-underline">Docs</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            License
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container">
            <p>
                AdminLTE is an open source project that is licensed under the
                <a href="#">MIT license</a>. This allows you to do pretty much
                anything you want as long as you include the copyright in “all
                copies or substantial portions of the Software.” Attribution is
                not required (though very much appreciated).
            </p>
            <h5 class="text-bold text-dark mt-3">
                What You Are <span class="text-success">Allowed</span> To Do
                With AdminLTE
            </h5>
            <ul>
                <li>Use in commercial projects.</li>
                <li>Use in personal/private projects.</li>
                <li>Modify and change the work.</li>
                <li>Distribute the code.</li>
                <li>
                    Sublicense: incorporate the work into something that has a
                    more restrictive license.
                </li>
            </ul>
        </div>
    </div>
</template>
