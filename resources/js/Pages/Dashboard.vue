<script setup>
import Header from '@/Components/Header.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, computed } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useToast } from "primevue/usetoast";
import { useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";

// const props = defineProps({
// 	orders: Array,
// });

// onMounted(() => {
// 	console.log('Datos de las órdenes:', props.orders);
// });

// // Calcula el total de órdenes
// const totalOrders = computed(() => props.orders.length);

// // Calcula las órdenes por estado usando 'filter' y 'length'
// const facturaPendienteCount = computed(() =>
// 	props.orders.filter(o => o.status === 'Factura pendiente').length
// );

// const cerradaCount = computed(() =>
// 	props.orders.filter(o => o.status === 'Cerrada').length
// );

// const facturacionPendienteCount = computed(() =>
// 	props.orders.filter(o => o.status === 'Facturación pendiente/Parcialmente recibido').length
// );

// const parcialmenteRecibidaCount = computed(() =>
// 	props.orders.filter(o => o.status === 'Parcialmente recibida').length
// );

// const recepcionPendienteCount = computed(() =>
// 	props.orders.filter(o => o.status === 'Recepción pendiente').length
// );

// const totalmenteFacturadaCount = computed(() =>
// 	props.orders.filter(o => o.status === 'Totalmente facturada').length
// );
// //******************IMPUESTOS********************** */
// // Propiedad computada para sumar el total de todos los impuestos
// const totalImpuestos = computed(() => {
// 	if (!props.orders || !Array.isArray(props.orders)) {
// 		return 0;
// 	}
// 	return props.orders.reduce((sum, order) => sum + parseFloat(order.impuesto || 0), 0);
// });

// // Suma de impuestos para cada estado
// const impuestosFacturaPendientes = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Factura pendiente')
// 		.reduce((sum, order) => sum + parseFloat(order.impuesto || 0), 0);
// });

// const impuestosCerrada = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Cerrada')
// 		.reduce((sum, order) => sum + parseFloat(order.impuesto || 0), 0);
// });

// const impuestosFacturacionPendiente = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Facturación pendiente/Parcialmente recibido')
// 		.reduce((sum, order) => sum + parseFloat(order.impuesto || 0), 0);
// });

// const impuestosParcialmenteRecibida = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Parcialmente recibida')
// 		.reduce((sum, order) => sum + parseFloat(order.impuesto || 0), 0);
// });

// const impuestosRecepcionPendiente = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Recepción pendiente')
// 		.reduce((sum, order) => sum + parseFloat(order.impuesto || 0), 0);
// });

// const impuestosTotalmenteFacturada = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Totalmente facturada')
// 		.reduce((sum, order) => sum + parseFloat(order.impuesto || 0), 0);
// });
// //******************IMPUESTOS FIN********************** */
// //******************SUBTOTAL********************** */
// // Propiedad computada para sumar el total de todos los impuestos
// const totalSubtotal = computed(() => {
// 	if (!props.orders || !Array.isArray(props.orders)) {
// 		return 0;
// 	}
// 	return props.orders.reduce((sum, order) => sum + parseFloat(order.subtotal || 0), 0);
// });

// // Suma de impuestos para cada estado
// const subtotalFacturaPendientes = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Factura pendiente')
// 		.reduce((sum, order) => sum + parseFloat(order.subtotal || 0), 0);
// });

// const subtotalCerrada = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Cerrada')
// 		.reduce((sum, order) => sum + parseFloat(order.subtotal || 0), 0);
// });

// const subtotalFacturacionPendiente = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Facturación pendiente/Parcialmente recibido')
// 		.reduce((sum, order) => sum + parseFloat(order.subtotal || 0), 0);
// });

// const subtotalParcialmenteRecibida = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Parcialmente recibida')
// 		.reduce((sum, order) => sum + parseFloat(order.subtotal || 0), 0);
// });

// const subtotalRecepcionPendiente = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Recepción pendiente')
// 		.reduce((sum, order) => sum + parseFloat(order.subtotal || 0), 0);
// });

// const subtotalTotalmenteFacturada = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Totalmente facturada')
// 		.reduce((sum, order) => sum + parseFloat(order.subtotal || 0), 0);
// });
// //******************SUBTOTAL FIN********************** */
// //******************TOTAL********************** */
// const total = computed(() => {
// 	if (!props.orders || !Array.isArray(props.orders)) {
// 		return 0;
// 	}
// 	return props.orders.reduce((sum, order) => sum + parseFloat(order.total || 0), 0);
// });

// // Suma de impuestos para cada estado
// const totalFacturaPendientes = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Factura pendiente')
// 		.reduce((sum, order) => sum + parseFloat(order.total || 0), 0);
// });

// const totalCerrada = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Cerrada')
// 		.reduce((sum, order) => sum + parseFloat(order.total || 0), 0);
// });

// const totalFacturacionPendiente = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Facturación pendiente/Parcialmente recibido')
// 		.reduce((sum, order) => sum + parseFloat(order.total || 0), 0);
// });

// const totalParcialmenteRecibida = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Parcialmente recibida')
// 		.reduce((sum, order) => sum + parseFloat(order.total || 0), 0);
// });

// const totalRecepcionPendiente = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Recepción pendiente')
// 		.reduce((sum, order) => sum + parseFloat(order.total || 0), 0);
// });

// const totalTotalmenteFacturada = computed(() => {
// 	if (!props.orders) return 0;
// 	return props.orders.filter(order => order.status === 'Totalmente facturada')
// 		.reduce((sum, order) => sum + parseFloat(order.total || 0), 0);
// });
</script>

<template>
	<AppLayout title="Dashboard">
		<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
			<Header :title="'Dashboard'" />
			<!-- <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
				<div class="container-fluid" id="kt_content_container">
					<div class="row g-5 g-lg-10">
						<div class="col-xxl-4 col-md-4 mb-xxl-10">
							<div class="card h-md-100">
								<div class="card-body p-0">
									<div class="px-9 pt-7 card-rounded h-275px w-100 bg-primary">
										<div class="d-flex flex-stack">
											<h3 class="m-0 text-white fw-bold fs-3">ORDENES</h3>
										</div>
										<div class="d-flex text-center flex-column text-white pt-8">
											<span class="fw-semibold fs-7">TOTAL DE ORDENES</span>
											<span class="fw-bold fs-2x pt-1">{{ totalOrders }}</span>
										</div>
									</div>
									<div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
										style="margin-top: -100px">
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-clock" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Factura
														pendientes</a>
													<div class="text-gray-500 fw-semibold fs-7">{{ facturaPendienteCount
													}}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-times-circle" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Cerrada</a>
													<div class="text-gray-500 fw-semibold fs-7">{{ cerradaCount }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-clock" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Facturación
														pendiente/Parcialmente recibido</a>
													<div class="text-gray-500 fw-semibold fs-7">{{
														facturacionPendienteCount }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-copy" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Parcialmete
														recibida</a>
													<div class="text-gray-500 fw-semibold fs-7">{{
														parcialmenteRecibidaCount }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-clock" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Recepción
														pendiente</a>
													<div class="text-gray-500 fw-semibold fs-7">{{
														recepcionPendienteCount }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-file-check" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Totalmente
														facturada</a>
													<div class="text-gray-500 fw-semibold fs-7">{{
														totalmenteFacturadaCount }}</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xxl-4 col-md-4 mb-xxl-10">
							<div class="card h-md-100">
								<div class="card-body p-0">
									<div class="px-9 pt-7 card-rounded h-275px w-100 bg-primary">
										<div class="d-flex flex-stack">
											<h3 class="m-0 text-white fw-bold fs-3">IMPUESTOS</h3>
										</div>
										<div class="d-flex text-center flex-column text-white pt-8">
											<span class="fw-semibold fs-7">TOTAL DE IMPUESTOS</span>
											<span class="fw-bold fs-2x pt-1">${{ totalImpuestos.toLocaleString('es-MX',
												{ minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
										</div>
									</div>
									<div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
										style="margin-top: -100px">
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-clock" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Factura
														pendientes</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														impuestosFacturaPendientes.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 })
														}}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-times-circle" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Cerrada</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														impuestosCerrada.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-clock" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Facturación
														pendiente/Parcialmente recibido</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														impuestosFacturacionPendiente.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-copy" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Parcialmete
														recibida</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														impuestosParcialmenteRecibida.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-clock" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Recepción
														pendiente</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														impuestosRecepcionPendiente.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-file-check" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Totalmente
														facturada</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														impuestosTotalmenteFacturada.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xxl-4 col-md-4 mb-xxl-10">
							<div class="card h-md-100">
								<div class="card-body p-0">
									<div class="px-9 pt-7 card-rounded h-275px w-100 bg-primary">
										<div class="d-flex flex-stack">
											<h3 class="m-0 text-white fw-bold fs-3">SUBTOTAL</h3>
										</div>
										<div class="d-flex text-center flex-column text-white pt-8">
											<span class="fw-semibold fs-7">TOTAL DE SUBTOTALES</span>
											<span class="fw-bold fs-2x pt-1">${{ totalSubtotal.toLocaleString('es-MX', {
												minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
										</div>
									</div>
									<div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
										style="margin-top: -100px">
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-clock" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Factura
														pendientes</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														subtotalFacturaPendientes.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 })
														}}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-times-circle" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Cerrada</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														subtotalCerrada.toLocaleString('es-MX', {
															minimumFractionDigits:
																2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-clock" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Facturación
														pendiente/Parcialmente recibido</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														subtotalFacturacionPendiente.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-copy" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Parcialmete
														recibida</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														subtotalParcialmenteRecibida.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-clock" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Recepción
														pendiente</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														subtotalRecepcionPendiente.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-file-check" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Totalmente
														facturada</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														subtotalTotalmenteFacturada.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xxl-4 col-md-4 mb-xxl-10">
							<div class="card h-md-100">
								<div class="card-body p-0">
									<div class="px-9 pt-7 card-rounded h-275px w-100 bg-primary">
										<div class="d-flex flex-stack">
											<h3 class="m-0 text-white fw-bold fs-3">TOTALES</h3>
										</div>
										<div class="d-flex text-center flex-column text-white pt-8">
											<span class="fw-semibold fs-7">TOTAL</span>
											<span class="fw-bold fs-2x pt-1">${{ total.toLocaleString('es-MX', {
												minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
										</div>
									</div>
									<div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
										style="margin-top: -100px">
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-clock" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Factura
														pendientes</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														totalFacturaPendientes.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 })
														}}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-times-circle" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Cerrada</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														totalCerrada.toLocaleString('es-MX', {
															minimumFractionDigits: 2,
														maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-clock" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Facturación
														pendiente/Parcialmente recibido</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														totalFacturacionPendiente.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-copy" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Parcialmete
														recibida</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														totalParcialmenteRecibida.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-clock" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Recepción
														pendiente</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														totalRecepcionPendiente.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
										<div class="d-flex align-items-center mb-6">
											<div class="symbol symbol-45px w-40px me-5">
												<span class="symbol-label bg-lighten">
													<i class="pi pi-file-check" style="font-size: 2rem"></i>
												</span>
											</div>
											<div class="d-flex align-items-center flex-wrap w-100">
												<div class="mb-1 pe-3 flex-grow-1">
													<a href="#"
														class="fs-5 text-gray-800 text-hover-primary fw-bold">Totalmente
														facturada</a>
													<div class="text-gray-500 fw-semibold fs-7">${{
														totalTotalmenteFacturada.toLocaleString('es-MX', {
															minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div> -->
			<div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
				<div class="container-fluid d-flex flex-column flex-md-row flex-stack">
					<div class="text-gray-900 order-2 order-md-1">
						<span class="text-gray-500 fw-semibold me-1">Created by</span>
						<a href="https://keenthemes.com" target="_blank"
							class="text-muted text-hover-primary fw-semibold me-2 fs-6">Keenthemes</a>
					</div>
					<ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
						<li class="menu-item">
							<a href="https://keenthemes.com" target="_blank" class="menu-link px-2">About</a>
						</li>
						<li class="menu-item">
							<a href="https://devs.keenthemes.com" target="_blank" class="menu-link px-2">Support</a>
						</li>
						<li class="menu-item">
							<a href="https://1.envato.market/EA4JP" target="_blank" class="menu-link px-2">Purchase</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="modal fade" id="kt_modal_invite_friends" tabindex="-1" aria-hidden="true">
			<!--begin::Modal dialog-->
			<div class="modal-dialog mw-650px">
				<!--begin::Modal content-->
				<div class="modal-content">
					<!--begin::Modal header-->
					<div class="modal-header pb-0 border-0 justify-content-end">
						<!--begin::Close-->
						<div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
							<i class="ki-duotone ki-cross fs-1">
								<span class="path1"></span>
								<span class="path2"></span>
							</i>
						</div>
						<!--end::Close-->
					</div>
					<!--begin::Modal header-->
					<!--begin::Modal body-->
					<div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
						<!--begin::Heading-->
						<div class="text-center mb-13">
							<!--begin::Title-->
							<h1 class="mb-3">Invite a Friend</h1>
							<!--end::Title-->
							<!--begin::Description-->
							<div class="text-muted fw-semibold fs-5">If you need more info, please check out
								<a href="#" class="link-primary fw-bold">FAQ Page</a>.
							</div>
							<!--end::Description-->
						</div>
						<!--end::Heading-->
						<!--begin::Google Contacts Invite-->
						<div class="btn btn-light-primary fw-bold w-100 mb-8">
							<img alt="Logo" src="assets/media/svg/brand-logos/google-icon.svg"
								class="h-20px me-3" />Invite Gmail Contacts
						</div>
						<!--end::Google Contacts Invite-->
						<!--begin::Separator-->
						<div class="separator d-flex flex-center mb-8">
							<span class="text-uppercase bg-body fs-7 fw-semibold text-muted px-3">or</span>
						</div>
						<!--end::Separator-->
						<!--begin::Textarea-->
						<textarea class="form-control form-control-solid mb-8" rows="3"
							placeholder="Type or paste emails here"></textarea>
						<!--end::Textarea-->
						<!--begin::Users-->
						<div class="mb-10">
							<!--begin::Heading-->
							<div class="fs-6 fw-semibold mb-2">Your Invitations</div>
							<!--end::Heading-->
							<!--begin::List-->
							<div class="mh-300px scroll-y me-n7 pe-7">
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<img alt="Pic" src="assets/media/avatars/300-6.jpg" />
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Emma
												Smith</a>
											<div class="fw-semibold text-muted">smith@kpmg.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2" selected="selected">Owner</option>
											<option value="3">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<span class="symbol-label bg-light-danger text-danger fw-semibold">M</span>
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#"
												class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Melody
												Macy</a>
											<div class="fw-semibold text-muted">melody@altbox.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1" selected="selected">Guest</option>
											<option value="2">Owner</option>
											<option value="3">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<img alt="Pic" src="assets/media/avatars/300-1.jpg" />
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Max
												Smith</a>
											<div class="fw-semibold text-muted">max@kt.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2">Owner</option>
											<option value="3" selected="selected">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<img alt="Pic" src="assets/media/avatars/300-5.jpg" />
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Sean
												Bean</a>
											<div class="fw-semibold text-muted">sean@dellito.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2" selected="selected">Owner</option>
											<option value="3">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<img alt="Pic" src="assets/media/avatars/300-25.jpg" />
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Brian
												Cox</a>
											<div class="fw-semibold text-muted">brian@exchange.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2">Owner</option>
											<option value="3" selected="selected">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<span
												class="symbol-label bg-light-warning text-warning fw-semibold">C</span>
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#"
												class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Mikaela
												Collins</a>
											<div class="fw-semibold text-muted">mik@pex.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2" selected="selected">Owner</option>
											<option value="3">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<img alt="Pic" src="assets/media/avatars/300-9.jpg" />
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#"
												class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Francis
												Mitcham</a>
											<div class="fw-semibold text-muted">f.mit@kpmg.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2">Owner</option>
											<option value="3" selected="selected">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<span class="symbol-label bg-light-danger text-danger fw-semibold">O</span>
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#"
												class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Olivia
												Wild</a>
											<div class="fw-semibold text-muted">olivia@corpmail.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2" selected="selected">Owner</option>
											<option value="3">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<span
												class="symbol-label bg-light-primary text-primary fw-semibold">N</span>
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Neil
												Owen</a>
											<div class="fw-semibold text-muted">owen.neil@gmail.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1" selected="selected">Guest</option>
											<option value="2">Owner</option>
											<option value="3">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<img alt="Pic" src="assets/media/avatars/300-23.jpg" />
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Dan
												Wilson</a>
											<div class="fw-semibold text-muted">dam@consilting.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2">Owner</option>
											<option value="3" selected="selected">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<span class="symbol-label bg-light-danger text-danger fw-semibold">E</span>
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Emma
												Bold</a>
											<div class="fw-semibold text-muted">emma@intenso.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2" selected="selected">Owner</option>
											<option value="3">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<img alt="Pic" src="assets/media/avatars/300-12.jpg" />
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Ana
												Crown</a>
											<div class="fw-semibold text-muted">ana.cf@limtel.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1" selected="selected">Guest</option>
											<option value="2">Owner</option>
											<option value="3">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<span class="symbol-label bg-light-info text-info fw-semibold">A</span>
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#"
												class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Robert
												Doe</a>
											<div class="fw-semibold text-muted">robert@benko.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2">Owner</option>
											<option value="3" selected="selected">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<img alt="Pic" src="assets/media/avatars/300-13.jpg" />
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">John
												Miller</a>
											<div class="fw-semibold text-muted">miller@mapple.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2">Owner</option>
											<option value="3" selected="selected">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<span
												class="symbol-label bg-light-success text-success fw-semibold">L</span>
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Lucy
												Kunic</a>
											<div class="fw-semibold text-muted">lucy.m@fentech.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2" selected="selected">Owner</option>
											<option value="3">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<img alt="Pic" src="assets/media/avatars/300-21.jpg" />
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Ethan
												Wilder</a>
											<div class="fw-semibold text-muted">ethan@loop.com.au</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1" selected="selected">Guest</option>
											<option value="2">Owner</option>
											<option value="3">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
								<!--begin::User-->
								<div class="d-flex flex-stack py-4">
									<!--begin::Details-->
									<div class="d-flex align-items-center">
										<!--begin::Avatar-->
										<div class="symbol symbol-35px symbol-circle">
											<img alt="Pic" src="assets/media/avatars/300-21.jpg" />
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Ethan
												Wilder</a>
											<div class="fw-semibold text-muted">ethan@loop.com.au</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm"
											data-control="select2" data-dropdown-parent="#kt_modal_invite_friends"
											data-hide-search="true">
											<option value="1">Guest</option>
											<option value="2">Owner</option>
											<option value="3" selected="selected">Can Edit</option>
										</select>
									</div>
									<!--end::Access menu-->
								</div>
								<!--end::User-->
							</div>
							<!--end::List-->
						</div>
						<!--end::Users-->
						<!--begin::Notice-->
						<div class="d-flex flex-stack">
							<!--begin::Label-->
							<div class="me-5 fw-semibold">
								<label class="fs-6">Adding Users by Team Members</label>
								<div class="fs-7 text-muted">If you need more info, please check budget planning</div>
							</div>
							<!--end::Label-->
							<!--begin::Switch-->
							<label class="form-check form-switch form-check-custom form-check-solid">
								<input class="form-check-input" type="checkbox" value="1" checked="checked" />
								<span class="form-check-label fw-semibold text-muted">Allowed</span>
							</label>
							<!--end::Switch-->
						</div>
						<!--end::Notice-->
					</div>
					<!--end::Modal body-->
				</div>
				<!--end::Modal content-->
			</div>
			<!--end::Modal dialog-->
		</div>

	</AppLayout>

</template>
