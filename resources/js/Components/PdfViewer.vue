<template>
    <div ref="container"></div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import * as pdfjsLib from 'pdfjs-dist';
import PdfjsWorker from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

const props = defineProps({
    pdfUrl: {
        type: String,
        required: true
    }
});

const container = ref(null);

// pdfjsLib.GlobalWorkerOptions.workerSrc = `//cdnjs.cloudflare.com/ajax/libs/pdf.js/${pdfjsLib.version}/pdf.worker.min.js`;
// pdfjsLib.GlobalWorkerOptions.workerSrc = '/resources/js/pdf.worker.min.mjs';
pdfjsLib.GlobalWorkerOptions.workerSrc = PdfjsWorker;

const renderPdf = async (url) => {
    if (!url) return;

    const loadingTask = pdfjsLib.getDocument(url);
    const pdf = await loadingTask.promise;

    for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
        const page = await pdf.getPage(pageNumber);
        const scale = 1.5;
        const viewport = page.getViewport({ scale });

        const canvas = document.createElement('canvas');
        const canvasContext = canvas.getContext('2d');
        canvas.height = viewport.height;
        canvas.width = viewport.width;

        container.value.appendChild(canvas);

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
    if (container.value) {
        container.value.innerHTML = ''; // Limpiar el contenedor antes de renderizar el nuevo PDF
    }
    renderPdf(newUrl);
});
</script>