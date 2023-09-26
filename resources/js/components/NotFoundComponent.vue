<template>
    <section class="flex items-center h-screen p-16">
        <div class="container flex flex-col items-center justify-center px-5 mx-auto my-8">
            <div class="max-w-md text-center">
                <h2 class="mb-8 font-extrabold text-9xl text-gray-400">
                    <span class="sr-only">Error</span>404
                </h2>
                <p class="text-2xl font-semibold">Sorry, we couldn't find this page.</p>
                <p class="mt-4 mb-8 ">But dont worry, you can find plenty of other things on our homepage.</p>
                <RouterLink :to="'/'">
                    <button class="px-8 py-3 font-semibold bg-main text-white rounded">Back to homepage</button>
                </RouterLink>
            </div>
        </div>
    </section>
</template>