document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('uploadForm');
  const fileInput = document.getElementById('file');
  const progressArea = document.getElementById('progressArea');
  const uploadBtn = document.getElementById('uploadBtn');

  if (!form) return;

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const files = fileInput.files;
    if (!files.length) return;
    progressArea.innerHTML = '';
    uploadBtn.disabled = true;
    let uploaded = 0;
    for (let i = 0; i < files.length; i++) {
      uploadSingleFile(files[i], i, files.length);
    }
  });

  function uploadSingleFile(file, idx, total) {
    const formData = new FormData();
    formData.append('file[]', file);
    // Tạo progress bar
    const wrapper = document.createElement('div');
    wrapper.style.marginBottom = '12px';
    wrapper.innerHTML = `<div style="margin-bottom:4px;">${file.name} (${(file.size/1048576).toFixed(2)} MB)</div>`;
    const progress = document.createElement('progress');
    progress.max = 100;
    progress.value = 0;
    progress.style.width = '100%';
    wrapper.appendChild(progress);
    progressArea.appendChild(wrapper);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'upload.php');
    xhr.upload.onprogress = function(e) {
      if (e.lengthComputable) {
        progress.value = Math.round((e.loaded / e.total) * 100);
      }
    };
    xhr.onload = function() {
      progress.value = 100;
      if (xhr.status === 200) {
        wrapper.innerHTML += '<div style="color:#6f6;">✔️ Thành công</div>';
      } else {
        wrapper.innerHTML += '<div style="color:#f66;">❌ Lỗi: ' + xhr.responseText + '</div>';
      }
      if (idx === total - 1) uploadBtn.disabled = false;
    };
    xhr.onerror = function() {
      wrapper.innerHTML += '<div style="color:#f66;">❌ Lỗi kết nối</div>';
      if (idx === total - 1) uploadBtn.disabled = false;
    };
    xhr.send(formData);
  }
});