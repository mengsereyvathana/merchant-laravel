<template>
  <div className='lg:py-7 lg:px-10 p-5'>
    <h1 className='text-3xl font-bold text-black_500'>Dashboard</h1>
    <div className="flex items-start md:items-center flex-col md:flex-row gap-5 mt-10 pb-6">

      <div className="flex gap-4 w-auto">
        <img src="/icons/1.png" alt="" className='w-[50px] h-[50px]' />
        <div className="">
          <h1 className='font-semibold text-lg text-black_500'>125 new orders</h1>
          <p className='text-gray-500 text-ph -mt-1'>Awating processing</p>
        </div>
      </div>
      <div className="flex gap-4 w-auto">
        <img src="/icons/2.png" alt="" className='w-[50px] h-[50px]' />
        <div className="">
          <h1 className='font-semibold text-lg text-black_500'>5 orders</h1>
          <p className='text-gray-500 text-ph -mt-1'>On hold</p>
        </div>
      </div>
      <div className="flex gap-4 w-auto">
        <img src="/icons/3.png" alt="" className='w-[50px] h-[50px]' />
        <div className="">
          <h1 className='font-semibold text-lg text-black_500'>12 products</h1>
          <p className='text-gray-500 text-ph -mt-1'>Out of stock</p>
        </div>
      </div>
    </div>
  </div>
</template>