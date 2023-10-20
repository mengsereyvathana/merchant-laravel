<template>
  <div>
    <h1 class='text-header text-grey-darken-2 font-weight-medium'>Dashboard</h1>
    <div className="flex items-start md:items-center flex-col md:flex-row gap-5 mt-10 pb-6">
      <div className="flex gap-4 w-auto">
        <v-icon></v-icon>
        <div className="">
          <h1 className='font-semibold text-lg text-black_500'>125 new orders</h1>
          <p className='text-gray-500 text-ph -mt-1'>Awating processing</p>
        </div>
      </div>
      <div className="flex gap-4 w-auto">
        <v-icon></v-icon>
        <div className="">
          <h1 className='font-semibold text-lg text-black_500'>5 orders</h1>
          <p className='text-gray-500 text-ph -mt-1'>On hold</p>
        </div>
      </div>
      <div className="flex gap-4 w-auto">
        <v-icon></v-icon>
        <div className="">
          <h1 className='font-semibold text-lg text-black_500'>12 products</h1>
          <p className='text-gray-500 text-ph -mt-1'>Out of stock</p>
        </div>
      </div>
    </div>
  </div>
</template>