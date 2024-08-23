document.getElementById('UploadForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = new FormData();
    formData.append('file', document.getElementById('file').files[0]);

    fetch('http://localhost:8000/upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('message').innerText = data;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('message').innerText = 'Upload failed.';
    });
});

