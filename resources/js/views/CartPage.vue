<script setup lang="ts">
import Swal from 'sweetalert2';
import { ref, onMounted, onBeforeMount, computed } from 'vue'
import { RouterLink, useRouter } from 'vue-router';
import { Cookie, Crypt, Upload } from '@/services/helper/index';
import { ICartItem } from '@/types/Cart';
import { CART_STORE } from '@/store/constants';
import { useStore } from '@/use/useStore';
import { orderService } from '@/services/api/modules/order.api';

const store = useStore();
const router = useRouter();

const payment_method = ref<string[]>(["ABA", "ACLEDA", "FTB"]);
const cart_loaded = ref<boolean>(false);

interface CartData {
    order_id: number,
    product_id: number | undefined,
    qty: number,
    unit_price: number,
    discount: number,
}

interface IForm {
    token: string | null;
    user_id: string | null;
}

let form = ref<IForm>({
    user_id: '',
    token: '',
})

const products = ref<ICartItem[]>([]);
const amount = ref<number>(0);

let selected_payment_method = ref<string>('');

onBeforeMount(() => {
    // checkLoaded();
})

onMounted(async () => {
    await getProductCart();
    if (payment_method.value.length > 0) {
        selected_payment_method.value = payment_method.value[0];
    }
})

let cartAmount = computed<number>(() => {
    return store.getters[CART_STORE.GETTERS.GET_CART_AMOUNT];
})

const getProductCart = async () => {
    await store.dispatch(CART_STORE.ACTIONS.GET_CART_ITEM_API, {})
    cart_loaded.value = true;
    products.value = store.getters[CART_STORE.GETTERS.GET_CART];
    amount.value = store.getters[CART_STORE.GETTERS.GET_CART_AMOUNT];
}

const plus = async (product_id: number) => {
    await store.dispatch(CART_STORE.ACTIONS.ADD_TO_CART, { product_id })
}

const minus = async (product_id: number, index: number) => {
    const product = products.value[index];
    if (product.qty === 1) {
        deleteFromCart(product_id, index);
    }
    else {
        await store.dispatch(CART_STORE.ACTIONS.MINUS_FROM_CART, { product_id })
    }
}

const deleteFromCart = async (product_id: number, index: number) => {
    await store.dispatch(CART_STORE.ACTIONS.DELETE_FROM_CART, { product_id, index })
}

const checkout = async () => {
    try {
        const result = await Swal.fire({
            title: 'Are you sure to checkout?',
            text: "Your order will be display in order page",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, checkout!'
        });
        if (result.isConfirmed) {
            form.value.user_id = Crypt.decrypt(Cookie.get("session_id") as string) as string;

            const formDataOrder = new FormData();
            formDataOrder.append("user_id", form.value.user_id);
            formDataOrder.append("address_id", "1");
            formDataOrder.append("pay_by", selected_payment_method.value);

            Swal.fire({
                position: 'center',
                allowEscapeKey: false,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            })
            const [error, data] = await orderService.addOrder(formDataOrder);
            if (error) console.log(error);
            else {
                if (data.success) {
                    const form: CartData[] = [];
                    for (let index = 0; index < products.value.length; index++) {
                        const item = {
                            order_id: data.data.id,
                            product_id: products.value[index].products?.id,
                            qty: products.value[index].qty,
                            unit_price: products.value[index].unit_price,
                            discount: 10,
                        }
                        form.push(item)
                    }

                    const [error, res] = await orderService.addOrderDetail(form);
                    if (error) console.log(error)
                    else {
                        if (res.success) {
                            getProductCart();
                            store.commit(CART_STORE.MUTATIONS.CLEAR_CART, 0);
                            Swal.close();
                            Swal.fire({
                                toast: true,
                                position: 'top',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1000,
                            })
                            router.push('/my_order');
                        }
                    }
                }
            }
        }
    } catch (error) {
        console.log(error);
    }
}

async function sendMessageTelegram(order_id: number, cartData: CartData[]) {
    // const TELEGRAM_BOT_TOKEN = "";
    // const TELEGRAM_GROUP_ID = "";

    // let text = `<b>Summary Order #${order_id}</b>` + '\n\n';
    // for (let index = 0; index < cartData.length; index++) {
    //     const product = products.value[index];
    //     text += (index + 1) + ". " + product.products?.name + "      x" + cartData[index].qty + "      $" + product.unit_price + "\n";
    // }
    // text += "-----------------------------------------" + "\n";
    // let total = 0;
    // for (let index = 0; index < cartData.length; index++) {
    //     total += cartData[index].qty * cartData[index].unit_price;
    // }
    // text += "Total:              $" + total.toFixed(2);
    // const data = {
    //     chat_id: TELEGRAM_GROUP_ID,
    //     parse_mode: "HTML",
    //     text: text
    // };

    // try {
    //     await Http.post(`https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/sendMessage`, data);
    // } catch (error) {
    //     console.log(error)
    // }
}

</script>
<template>
    <div v-if="cart_loaded && products.length > 0" class="flex flex-col p-4 gap-3 bg-white">
        <div class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-lg">
            <div class="p-4">
                <h3 class=" border-b border-solid border-gray-200 uppercase pb-4 font-semibold">items in cart</h3>
            </div>
            <div class="overflow-x-auto scrollbar-thin">
                <div v-for="(item, index) in products" :key="item.id" :data-index="index"
                    class=" flex justify-between p-4 border-b-[1px] last:border-b-0 first:pt-0 border-gray-200 border-solid gap-2">
                    <div class="flex gap-3" v-if="item.products">
                        <div class="min-w-[4rem] h-[4rem] w-[4rem]">
                            <img :src="Upload.image(`${item.products.image}`)"
                                class="w-full h-full bg-[#f3f5f9] rounded text-center leading-10 cursor-pointer object-cover" />
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-sm text-dark text-gray-600 font-semibold">
                                {{ item.products.name }}
                            </h1>
                            <p class="text-sm text-letter mt-1 font-semibold">
                                <span>${{ item.unit_price.toFixed(2) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col justify-between" v-if="item.products">
                        <div class="flex justify-between items-center">
                            <div class="font-semibold text-center text-sm">${{ item.total }}
                            </div>
                            <button @click="deleteFromCart(item.products.id, index)" class="w-[35px] h-[30px]">
                                <img :src="Upload.icon('trash.svg')" alt="" class="w-[15px] h-[15px] mx-auto">
                            </button>
                        </div>
                        <div class="flex">
                            <button @click="minus(item.products.id, index)" v-ripple-init
                                class="w-[30px] h-[30px] border border-solid shadow-sm rounded-full ripple-effect"
                                dir="ltr">
                                <img :src="Upload.icon('minus.svg')" alt="" class="w-[15px] h-[15px] m-auto" />
                            </button>
                            <div class="w-[30px] h-[30px] flex justify-center items-center">
                                <span class="font-medium text-sm">{{ item.qty }}</span>
                            </div>
                            <button @click="plus(item.products.id)" v-ripple-init
                                class="w-[30px] h-[30px] border border-solid shadow-sm rounded-full ripple-effect"
                                dir="rtl">
                                <img :src="Upload.icon('plus.svg')" alt="" class="w-[15px] h-[15px] m-auto" />
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white shadow-lg first:border-t-0 p-4 rounded-lg mb-[128px]">
            <h3 class="uppercase pb-4 font-semibold">Bill details</h3>
            <div class="border-t border-solid flex justify-between items-center py-4">
                <p class="capitalize text-sm">item total</p>
                <p class="font-semibold text-sm">${{ cartAmount.toFixed(2) }}</p>
            </div>
            <div class="border-t border-solid flex flex-col justify-start py-4">
                <p class="capitalize text-sm mb-3">Payment Method</p>
                <select v-if="payment_method.length > 0" name="" id="" class="w-full mt-1 text-[14px] cursor-pointer"
                    v-model="selected_payment_method">
                    <option v-for="(item, index) in payment_method" :value="item" :key="index">{{ item }}</option>
                </select>
            </div>
        </div>
        <div
            class="fixed flex flex-col item-center bottom-0 left-0 right-0 w-full bg-white shadow-lg first:border-t-0 rounded-lg">
            <!-- <div class="p-4">
                <div class="flex flex-row justify-between item-center">
                    <h3 class="uppercase font-semibold">Deliver to</h3>
                    <RouterLink to="/cart/my_address">
                        <button class="w-[55px] h-[30px] border border-solid shadow-sm text-xs font-semibold text-main">
                            Change
                        </button>
                    </RouterLink>
                </div>
                <p class="text-xs">{{ address.address }}</p>
            </div> -->
            <button @click="checkout()" class="w-full uppercase p-4 bg-main text-white text-center ripple-effect"
                v-ripple-init><span class="font-semibold">Proceed to
                    checkout</span>
            </button>
        </div>
    </div>
    <div v-else-if="cart_loaded && products.length === 0"
        class='mycontainer h-[75vh] flex justify-center items-center flex-col'>
        <!-- <img :src="'/icons/empty.png'" alt="" class='w-[300px] h-[300px]' /> -->
        <h1 class='text-lg md:text-4xl font-semibold mt-10 '>Your Cart is <span class='text-main'>Empty!</span>
        </h1>
        <RouterLink to='/'>
            <button class='text-lg md:text-xl font-semibold text-white bg-main px-[50px] py-2 rounded-full mt-10'>Back to
                shop</button>
        </RouterLink>
    </div>
</template>
<style>
/* .list-enter-active,
.list-leave-active {
    transition: opacity 0.5s, transform 0.5s;
}

.list-enter-from,
.list-leave-to {
    opacity: 0;
    transform: translateX(-100px);
} */
.bgasdfasdf {
    background-color: #4e4948;
}
</style>
