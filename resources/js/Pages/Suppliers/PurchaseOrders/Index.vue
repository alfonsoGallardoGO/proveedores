<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    orders: Array,
});

const modalVisible = ref(false);
const selectedOrder = ref(null);

const show = async (id) => {
    try {
        const response = await axios.get(route("purchase-orders.show", id));
        selectedOrder.value = response.data;
        modalVisible.value = true;
    } catch (error) {
        console.error("Error al cargar la orden:", error);
    }
};



</script>

<template>
    {{ orders }}
    <AppLayout title="Ordenes de compra">
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <Header :title="'Ordenes de compra'" />
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <div class="container-fluid" id="kt_content_container">
                    <div class="row g-4">
                        <div class="col-12 col-md-3 col-lg-3" v-for="order in orders" :key="order.id" >
                            <Card style="width: 100%; overflow: hidden">
                                <template #header>
                                    <img alt="user header" src="https://cdn.worldvectorlogo.com/logos/netsuite.svg" />
                                </template>
                                
                                <template #title>Orden de compra: {{ order.data?.tranid }}</template>
                                <template #subtitle>Fecha: {{ order.data?.fecha }}</template>
                                <template #footer>
                                    <div class="d-flex gap-2 mt-1">
                                        <Button label="Ver" severity="secondary" outlined class="w-50"  @click="show(order.id)"/>
                                        <Button label="XML" class="w-50" />
                                        <Button label="PDF" class="w-50" />
                                    </div>
                                </template>
                            </Card>
                            
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
    <Modal :show="modalVisible" @close="close" maxWidth="2xl">
        <div class="p-6 space-y-6">
        
            <div class="flex items-center justify-between border-b pb-3">
                <h2 class="text-2xl font-semibold text-gray-800">
                    Orden de compra: 
                    <span class="text-blue-600">{{ selectedOrder?.data?.tranid }}</span>
                </h2>
                <span 
                    class="px-3 py-1 text-sm rounded-full"
                    :class="{
                        'bg-yellow-100 text-yellow-800': selectedOrder?.estado === 'Recepción pendiente',
                        'bg-green-100 text-green-800': selectedOrder?.estado === 'Completada',
                        'bg-red-100 text-red-800': selectedOrder?.estado === 'Cancelada'
                    }"
                >
                    {{ selectedOrder?.data?.estado }}
                </span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Fecha</p>
                    <p class="font-medium">{{ selectedOrder?.data.fecha }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="font-bold text-green-600">
                        ${{ selectedOrder?.data.total?.toLocaleString() }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Proveedor</p>
                    <p class="font-medium">{{ selectedOrder?.data?.proveedor.nombre }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">RFC</p>
                    <p class="font-medium">{{ selectedOrder?.data?.proveedor.rfc }}</p>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-3">Artículos</h3>
                <div class="overflow-x-auto border rounded-lg">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-2">Descripción</th>
                                <th class="px-4 py-2">Cantidad</th>
                                <th class="px-4 py-2">Importe</th>
                                <th class="px-4 py-2">Clase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="linea in selectedOrder?.data?.lineasArticulos" 
                                :key="linea.articuloId"
                                class="border-t hover:bg-gray-50"
                            >
                                <td class="px-4 py-2">{{ linea.descripcion }}</td>
                                <td class="px-4 py-2">{{ linea.cantidad }}</td>
                                <td class="px-4 py-2">${{ linea.importe?.toLocaleString() }}</td>
                                <td class="px-4 py-2">{{ linea.clase }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-3">Gastos</h3>
                <div class="overflow-x-auto border rounded-lg">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-2">Descripción</th>
                                <th class="px-4 py-2">Cantidad</th>
                                <th class="px-4 py-2">Importe</th>
                                <th class="px-4 py-2">Clase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="gasto in selectedOrder?.data?.lineasGastos" 
                                :key="gasto.articuloId"
                                class="border-t hover:bg-gray-50"
                            >
                                <td class="px-4 py-2">{{ gasto.memo }}</td>
                                <td class="px-4 py-2">{{ gasto.cantidad }}</td>
                                <td class="px-4 py-2">${{ gasto.importe?.toLocaleString() }}</td>
                                <td class="px-4 py-2">{{ gasto.clase }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end space-x-3 border-t pt-4">
                <Button label="Cerrar" severity="secondary" @click="close" />
                <Button label="Guardar" icon="pi pi-save" />
            </div>
        </div>
    </Modal>


</template>
