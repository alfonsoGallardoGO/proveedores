<script setup>
import Header from '@/Components/Header.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from "vue";




onMounted(() => {
    chartData.value = setChartData();
    chartOptions.value = setChartOptions();
    chartDataDoughnut.value = setChartDataDoughnut();
    chartOptionsDoughnut.value = setChartOptionsDoughnut();
});

const chartData = ref();
const chartOptions = ref();
        
const setChartData = () => {
    const documentStyle = getComputedStyle(document.documentElement);

    return {
        labels: ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULYO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'],
        datasets: [
            {
                label: 'ARTICULO',
                data: [65, 59, 80, 81, 56, 55, 40, 65, 59, 80, 81, 56],
                fill: false,
                tension: 0.4,
                borderColor: documentStyle.getPropertyValue('--p-cyan-500')
            },
            {
                label: 'GASTOS',
                data: [28, 48, 40, 19, 86, 27, 90, 28, 48, 40, 19, 86],
                fill: false,
                borderDash: [5, 5],
                tension: 0.4,
                borderColor: documentStyle.getPropertyValue('--p-orange-500')
            },
            {
                label: 'MONTO TOTAL MES',
                data: [12, 51, 62, 33, 21, 62, 45, 12, 51, 62, 33, 21],
                fill: true,
                borderColor: documentStyle.getPropertyValue('--p-gray-500'),
                tension: 0.4,
                backgroundColor: 'rgba(107, 114, 128, 0.2)'
            }
        ]
    };
};
const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--p-text-muted-color');
    const surfaceBorder = documentStyle.getPropertyValue('--p-content-border-color');

    return {
        maintainAspectRatio: false,
        aspectRatio: 0.6,
        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            },
            y: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            }
        }
    };
}

// ****************************


const chartDataDoughnut = ref();
const chartOptionsDoughnut = ref(null);

const setChartDataDoughnut = () => {
    const documentStyle = getComputedStyle(document.body);

    return {
        labels: ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'],
        datasets: [
            {
                data: [540, 325, 702, 540, 325, 702, 540, 325, 702, 540, 325, 702],
                backgroundColor: [documentStyle.getPropertyValue('--p-cyan-500'), documentStyle.getPropertyValue('--p-orange-500'), documentStyle.getPropertyValue('--p-gray-500')],
                hoverBackgroundColor: [documentStyle.getPropertyValue('--p-cyan-400'), documentStyle.getPropertyValue('--p-orange-400'), documentStyle.getPropertyValue('--p-gray-400')]
            }
        ]
    };
};

const setChartOptionsDoughnut = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');

    return {
        plugins: {
            legend: {
                labels: {
                    cutout: '60%',
                    color: textColor
                }
            }
        }
    };
};



</script>



<template>
    <AppLayout title="Dashboard">

        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">

            <Header :title="'Dashboard'" />

            <!--begin::Content-->
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <!--begin::Container-->
                <div class="container-fluid" id="kt_content_container">
                    <!--begin::Row-->
                    <div class="row g-5 g-lg-10">
                        <!--begin::Col-->
                        <div class="col-xxl-4 col-md-4 mb-xxl-10">
                            <!--begin::Mixed Widget 1-->
                            <div class="card h-md-100">
                                <!--begin::Body-->
                                <div class="card-body p-0">
                                    <!--begin::Header-->
                                    <div class="px-9 pt-7 card-rounded h-275px w-100 bg-primary">
                                        <!--begin::Heading-->
                                        <div class="d-flex flex-stack">
                                            <h3 class="m-0 text-white fw-bold fs-3">ORDENES</h3>
                                            <!-- <div class="ms-1">
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-color-white btn-active-white border-0 me-n3"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <i class="pi pi-bars" style="font-size: 2rem"></i>
                                                </button>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                                    data-kt-menu="true">
                                                    <div class="menu-item px-3">
                                                        <div
                                                            class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                            Payments</div>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Create Invoice</a>
                                                    </div>
                                                    
                                                    <div class="menu-item px-3 my-1">
                                                        <a href="#" class="menu-link px-3">Settings</a>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div>
                                        <div class="d-flex text-center flex-column text-white pt-8">
                                            <span class="fw-semibold fs-7">TOTAL DE ORDENES</span>
                                            <span class="fw-bold fs-2x pt-1">39</span>
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
                                                        class="fs-5 text-gray-800 text-hover-primary fw-bold">Factura pendientes</a>
                                                    <div class="text-gray-500 fw-semibold fs-7">2</div>
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
                                                    <div class="text-gray-500 fw-semibold fs-7">1</div>
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
                                                        class="fs-5 text-gray-800 text-hover-primary fw-bold">Facturación pendiente/Parcialmente recibido</a>
                                                    <div class="text-gray-500 fw-semibold fs-7">0</div>
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
                                                        class="fs-5 text-gray-800 text-hover-primary fw-bold">Parcialmete recibida</a>
                                                    <div class="text-gray-500 fw-semibold fs-7">2</div>
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
                                                        class="fs-5 text-gray-800 text-hover-primary fw-bold">Recepción pendiente</a>
                                                    <div class="text-gray-500 fw-semibold fs-7">33</div>
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
                                                        class="fs-5 text-gray-800 text-hover-primary fw-bold">Totalmente facturada</a>
                                                    <div class="text-gray-500 fw-semibold fs-7">1</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<div class="col-xxl-4 col-md-4 mb-xxl-10">
                            <!--begin::Mixed Widget 1-->
                            <div class="card h-md-100">
                                <!--begin::Body-->
                                <div class="card-body p-0">
                                    <!--begin::Header-->
                                    <div class="px-9 pt-7 card-rounded h-275px w-100 bg-primary">
                                        <!--begin::Heading-->
                                        <div class="d-flex flex-stack">
                                            <h3 class="m-0 text-white fw-bold fs-3">COSTOS</h3>
                                            <div class="ms-1">
                                                <!--begin::Menu-->
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-color-white btn-active-white border-0 me-n3"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <i class="pi pi-bars" style="font-size: 2rem"></i>
                                                </button>
                                                <!--begin::Menu 3-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                                    data-kt-menu="true">
                                                    <!--begin::Heading-->
                                                    <div class="menu-item px-3">
                                                        <div
                                                            class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                            Payments</div>
                                                    </div>
                                                    <!--end::Heading-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Create Invoice</a>
                                                    </div>
                                                    
                                                    <div class="menu-item px-3 my-1">
                                                        <a href="#" class="menu-link px-3">Settings</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 3-->
                                                <!--end::Menu-->
                                            </div>
                                        </div>
                                        <!--end::Heading-->
                                        <!--begin::Balance-->
                                        <div class="d-flex text-center flex-column text-white pt-8">
                                            <span class="fw-semibold fs-7">TOTAL DE GASTO DEL MES</span>
                                            <span class="fw-bold fs-2x pt-1">$37,562.00</span>
                                        </div>
                                        <!--end::Balance-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Items-->
                                    <div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
                                        style="margin-top: -100px">
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center mb-6">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-45px w-40px me-5">
                                                <span class="symbol-label bg-lighten">
                                                    <i class="pi pi-clock" style="font-size: 2rem"></i>
                                                </span>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Description-->
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <!--begin::Title-->
                                                <div class="mb-1 pe-3 flex-grow-1">
                                                    <a href="#"
                                                        class="fs-5 text-gray-800 text-hover-primary fw-bold">Ordenes pendientes</a>
                                                    <div class="text-gray-500 fw-semibold fs-7">100 Regions</div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Item-->
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center mb-6">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-45px w-40px me-5">
                                                <span class="symbol-label bg-lighten">
                                                    <i class="pi pi-dollar" style="font-size: 2rem"></i>
                                                </span>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Description-->
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <!--begin::Title-->
                                                <div class="mb-1 pe-3 flex-grow-1">
                                                    <a href="#"
                                                        class="fs-5 text-gray-800 text-hover-primary fw-bold">Valor Ordenes Pendientes</a>
                                                    <div class="text-gray-500 fw-semibold fs-7">Quarter 2/3</div>
                                                </div>
                                            </div>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Item-->
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center mb-6">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-45px w-40px me-5">
                                                <span class="symbol-label bg-lighten">
                                                    <i class="pi pi-clock" style="font-size: 2rem"></i>
                                                </span>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Description-->
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <!--begin::Title-->
                                                <div class="mb-1 pe-3 flex-grow-1">
                                                    <a href="#"
                                                        class="fs-5 text-gray-800 text-hover-primary fw-bold">Facturas Pendientes</a>
                                                    <div class="text-gray-500 fw-semibold fs-7">80% Rate</div>
                                                </div>
                                            </div>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                    <!--end::Items-->
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Mixed Widget 1-->
                        </div>
						<div class="col-xxl-4 col-md-4 mb-xxl-10">
                            <!--begin::Mixed Widget 1-->
                            <div class="card h-md-100">
                                <!--begin::Body-->
                                <div class="card-body p-0">
                                    <!--begin::Header-->
                                    <div class="px-9 pt-7 card-rounded h-275px w-100 bg-primary">
                                        <!--begin::Heading-->
                                        <div class="d-flex flex-stack">
                                            <h3 class="m-0 text-white fw-bold fs-3">Resumen Ejecutivo</h3>
                                            <div class="ms-1">
                                                <!--begin::Menu-->
                                                <button type="button"
                                                    class="btn btn-sm btn-icon btn-color-white btn-active-white border-0 me-n3"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <i class="pi pi-bars" style="font-size: 2rem"></i>
                                                </button>
                                                <!--begin::Menu 3-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                                    data-kt-menu="true">
                                                    <!--begin::Heading-->
                                                    <div class="menu-item px-3">
                                                        <div
                                                            class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                            Payments</div>
                                                    </div>
                                                    <!--end::Heading-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Create Invoice</a>
                                                    </div>
                                                    
                                                    <div class="menu-item px-3 my-1">
                                                        <a href="#" class="menu-link px-3">Settings</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 3-->
                                                <!--end::Menu-->
                                            </div>
                                        </div>
                                        <!--end::Heading-->
                                        <!--begin::Balance-->
                                        <div class="d-flex text-center flex-column text-white pt-8">
                                            <span class="fw-semibold fs-7">TOTAL DE GASTO DEL MES</span>
                                            <span class="fw-bold fs-2x pt-1">$37,562.00</span>
                                        </div>
                                        <!--end::Balance-->
                                    </div>
                                    <!--end::Header-->
                                    <!--begin::Items-->
                                    <div class="bg-body shadow-sm card-rounded mx-9 mb-9 px-6 py-9 position-relative z-index-1"
                                        style="margin-top: -100px">
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center mb-6">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-45px w-40px me-5">
                                                <span class="symbol-label bg-lighten">
                                                    <i class="pi pi-clock" style="font-size: 2rem"></i>
                                                </span>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Description-->
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <!--begin::Title-->
                                                <div class="mb-1 pe-3 flex-grow-1">
                                                    <a href="#"
                                                        class="fs-5 text-gray-800 text-hover-primary fw-bold">Ordenes pendientes</a>
                                                    <div class="text-gray-500 fw-semibold fs-7">100 Regions</div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Item-->
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center mb-6">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-45px w-40px me-5">
                                                <span class="symbol-label bg-lighten">
                                                    <i class="pi pi-dollar" style="font-size: 2rem"></i>
                                                </span>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Description-->
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <!--begin::Title-->
                                                <div class="mb-1 pe-3 flex-grow-1">
                                                    <a href="#"
                                                        class="fs-5 text-gray-800 text-hover-primary fw-bold">Valor Ordenes Pendientes</a>
                                                    <div class="text-gray-500 fw-semibold fs-7">Quarter 2/3</div>
                                                </div>
                                            </div>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Item-->
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center mb-6">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-45px w-40px me-5">
                                                <span class="symbol-label bg-lighten">
                                                    <i class="pi pi-clock" style="font-size: 2rem"></i>
                                                </span>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Description-->
                                            <div class="d-flex align-items-center flex-wrap w-100">
                                                <!--begin::Title-->
                                                <div class="mb-1 pe-3 flex-grow-1">
                                                    <a href="#"
                                                        class="fs-5 text-gray-800 text-hover-primary fw-bold">Facturas Pendientes</a>
                                                    <div class="text-gray-500 fw-semibold fs-7">80% Rate</div>
                                                </div>
                                            </div>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                    <!--end::Items-->
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Mixed Widget 1-->
                        </div>
                        
                        
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::Content-->
            <!--begin::Footer-->
            <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
                <!--begin::Container-->
                <div class="container-fluid d-flex flex-column flex-md-row flex-stack">
                    <!--begin::Copyright-->
                    <div class="text-gray-900 order-2 order-md-1">
                        <span class="text-gray-500 fw-semibold me-1">Created by</span>
                        <a href="https://keenthemes.com" target="_blank"
                            class="text-muted text-hover-primary fw-semibold me-2 fs-6">Keenthemes</a>
                    </div>
                    <!--end::Copyright-->
                    <!--begin::Menu-->
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
                    <!--end::Menu-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::Footer-->
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
							<a href="#" class="link-primary fw-bold">FAQ Page</a>.</div>
							<!--end::Description-->
						</div>
						<!--end::Heading-->
						<!--begin::Google Contacts Invite-->
						<div class="btn btn-light-primary fw-bold w-100 mb-8">
						<img alt="Logo" src="assets/media/svg/brand-logos/google-icon.svg" class="h-20px me-3" />Invite Gmail Contacts</div>
						<!--end::Google Contacts Invite-->
						<!--begin::Separator-->
						<div class="separator d-flex flex-center mb-8">
							<span class="text-uppercase bg-body fs-7 fw-semibold text-muted px-3">or</span>
						</div>
						<!--end::Separator-->
						<!--begin::Textarea-->
						<textarea class="form-control form-control-solid mb-8" rows="3" placeholder="Type or paste emails here"></textarea>
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Emma Smith</a>
											<div class="fw-semibold text-muted">smith@kpmg.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Melody Macy</a>
											<div class="fw-semibold text-muted">melody@altbox.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Max Smith</a>
											<div class="fw-semibold text-muted">max@kt.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Sean Bean</a>
											<div class="fw-semibold text-muted">sean@dellito.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Brian Cox</a>
											<div class="fw-semibold text-muted">brian@exchange.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<span class="symbol-label bg-light-warning text-warning fw-semibold">C</span>
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Mikaela Collins</a>
											<div class="fw-semibold text-muted">mik@pex.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Francis Mitcham</a>
											<div class="fw-semibold text-muted">f.mit@kpmg.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Olivia Wild</a>
											<div class="fw-semibold text-muted">olivia@corpmail.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<span class="symbol-label bg-light-primary text-primary fw-semibold">N</span>
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Neil Owen</a>
											<div class="fw-semibold text-muted">owen.neil@gmail.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Dan Wilson</a>
											<div class="fw-semibold text-muted">dam@consilting.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Emma Bold</a>
											<div class="fw-semibold text-muted">emma@intenso.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Ana Crown</a>
											<div class="fw-semibold text-muted">ana.cf@limtel.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Robert Doe</a>
											<div class="fw-semibold text-muted">robert@benko.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">John Miller</a>
											<div class="fw-semibold text-muted">miller@mapple.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<span class="symbol-label bg-light-success text-success fw-semibold">L</span>
										</div>
										<!--end::Avatar-->
										<!--begin::Details-->
										<div class="ms-5">
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Lucy Kunic</a>
											<div class="fw-semibold text-muted">lucy.m@fentech.com</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Ethan Wilder</a>
											<div class="fw-semibold text-muted">ethan@loop.com.au</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
											<a href="#" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Ethan Wilder</a>
											<div class="fw-semibold text-muted">ethan@loop.com.au</div>
										</div>
										<!--end::Details-->
									</div>
									<!--end::Details-->
									<!--begin::Access menu-->
									<div class="ms-2 w-100px">
										<select class="form-select form-select-solid form-select-sm" data-control="select2" data-dropdown-parent="#kt_modal_invite_friends" data-hide-search="true">
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
