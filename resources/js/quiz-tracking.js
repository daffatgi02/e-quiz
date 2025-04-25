function updateQuizStatus() {
    fetch(`/admin/quizzes/${quizId}/status`)
        .then(response => response.json())
        .then(data => {
            // Update tampilan dengan data terbaru
            updateStatusTable(data);
        });
}

// Update setiap 30 detik
setInterval(updateQuizStatus, 30000);
