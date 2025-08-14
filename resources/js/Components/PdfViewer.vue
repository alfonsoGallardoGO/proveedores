<script setup>
import { ref, onMounted, watch } from 'vue';
import * as pdfjsLib from 'pdfjs-dist';

const props = defineProps({
    pdfUrl: {
        type: String,
        required: true
    }
});

const container = ref(null);

// Esta es la forma correcta de asignar la ruta estática del worker
pdfjsLib.GlobalWorkerOptions.workerSrc = '/build/assets/pdf.worker.min.mjs';

const renderPdf = async (url) => {
    if (!url) return;

    const loadingTask = pdfjsLib.getDocument(url);
    const pdf = await loadingTask.promise;

    // Limpia el contenedor antes de renderizar
    if (container.value) {
        container.value.innerHTML = '';
    }

    for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
        const page = await pdf.getPage(pageNumber);
        const scale = 1.5;
        const viewport = page.getViewport({ scale });

        const canvas = document.createElement('canvas');
        const canvasContext = canvas.getContext('2d');
        canvas.height = viewport.height;
        canvas.width = viewport.width;

        if (container.value) {
            container.value.appendChild(canvas);
        }

        const renderContext = {
            canvasContext,
            viewport,
        };
        await page.render(renderContext).promise;
    }
};

onMounted(() => {
    renderPdf(props.pdfUrl);
});

watch(() => props.pdfUrl, (newUrl) => {
    renderPdf(newUrl);
});
</script>

<template>
    <div ref="container" style="width: 100%; height: 600px; overflow-y: auto;"></div>
</template>