document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    const fileInput = document.getElementById('fileInput');
    const uploadBtn = document.getElementById('uploadBtn');
    const fileURLInput = document.getElementById('fileURL');
    const copyURLBtn = document.getElementById('copyURLBtn');

    uploadForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.fileURL) {
                fileURLInput.style.display = 'block';
                fileURLInput.value = data.fileURL;
                copyURLBtn.style.display = 'block';
            } else if (data.error) {
                alert(data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    copyURLBtn.addEventListener('click', function() {
        fileURLInput.select();
        document.execCommand('copy');
        alert('URL copied to clipboard');
    });
});
