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
    uploadMultiFiles(Array.from(files));
  });

  function uploadMultiFiles(files, actions = null) {
    let formData = new FormData();
    files.forEach(f => formData.append('file[]', f));
    if (actions) formData.append('actions', JSON.stringify(actions));
    // Hiển thị progress cho từng file
    progressArea.innerHTML = '';
    let wrappers = files.map(file => {
      const wrapper = document.createElement('div');
      wrapper.style.marginBottom = '12px';
      wrapper.innerHTML = `<div style="margin-bottom:4px;">${file.name} (${(file.size/1048576).toFixed(2)} MB)</div>`;
      const progress = document.createElement('progress');
      progress.max = 100;
      progress.value = 0;
      progress.style.width = '100%';
      wrapper.appendChild(progress);
      progressArea.appendChild(wrapper);
      return {wrapper, file, progress};
    });
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'upload.php');
    xhr.onload = function() {
      uploadBtn.disabled = false;
      if (xhr.status === 409) {
        // Có file trùng tên
        let resp = {};
        try { resp = JSON.parse(xhr.responseText); } catch(e){}
        if (resp.conflicts && Array.isArray(resp.conflicts)) {
          showConflictModal(resp.conflicts, files, actions);
        } else {
          progressArea.innerHTML += '<div style="color:#f66;">Lỗi không xác định!</div>';
        }
        return;
      }
      let results = [];
      try { results = JSON.parse(xhr.responseText); } catch(e){}
      if (!Array.isArray(results)) results = [xhr.responseText];
      wrappers.forEach((w, i) => {
        w.progress.value = 100;
        let msg = results[i] || '';
        if (msg.includes('thành công')) {
          w.wrapper.innerHTML += '<div style="color:#6f6;">✔️ ' + msg + '</div>';
        } else {
          w.wrapper.innerHTML += '<div style="color:#f66;">❌ ' + msg + '</div>';
        }
      });
    };
    xhr.onerror = function() {
      uploadBtn.disabled = false;
      progressArea.innerHTML += '<div style="color:#f66;">❌ Lỗi kết nối</div>';
    };
    xhr.upload.onprogress = function(e) {
      if (e.lengthComputable) {
        wrappers.forEach(w => w.progress.value = Math.round((e.loaded / e.total) * 100));
      }
    };
    xhr.send(formData);
  }

  function showConflictModal(conflicts, files, prevActions) {
    // Tạo modal chọn hành động cho từng file trùng
    let modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.left = 0;
    modal.style.top = 0;
    modal.style.width = '100vw';
    modal.style.height = '100vh';
    modal.style.background = 'rgba(0,0,0,0.7)';
    modal.style.zIndex = 9999;
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    let box = document.createElement('div');
    box.style.background = '#232526';
    box.style.borderRadius = '12px';
    box.style.padding = '28px 24px';
    box.style.maxWidth = '95vw';
    box.style.boxShadow = '0 4px 32px #000a';
    box.innerHTML = `<h3 style='color:#fff;margin-top:0;'>Có file trùng tên</h3>`;
    let actions = prevActions ? {...prevActions} : {};
    conflicts.forEach(filename => {
      let row = document.createElement('div');
      row.style.margin = '12px 0';
      row.innerHTML = `<b style='color:#fff;'>${filename}</b> đã tồn tại.<br>Chọn hành động: `;
      let select = document.createElement('select');
      select.style.marginLeft = '8px';
      select.innerHTML = `<option value="overwrite">Ghi đè</option><option value="rename">Đổi tên tự động</option><option value="skip">Không upload</option>`;
      row.appendChild(select);
      box.appendChild(row);
      actions[filename] = 'overwrite';
      select.onchange = function(){ actions[filename] = this.value; };
    });
    let btn = document.createElement('button');
    btn.textContent = 'Tiếp tục upload';
    btn.style.marginTop = '18px';
    btn.style.background = '#444';
    btn.style.color = '#fff';
    btn.style.border = 'none';
    btn.style.borderRadius = '8px';
    btn.style.padding = '10px 24px';
    btn.style.fontSize = '1rem';
    btn.style.cursor = 'pointer';
    btn.onclick = function() {
      document.body.removeChild(modal);
      uploadMultiFiles(files, actions);
    };
    box.appendChild(btn);
    modal.appendChild(box);
    document.body.appendChild(modal);
  }
});