/* SI Project Tracker - Modern View JavaScript */
jQuery(document).ready(function ($) {

  if (!$('.si-pt-app').length) {
    return; // Modern shell isn't on this page; nothing to do.
  }

  function openModal($modal) {
    $modal.addClass('si-pt-open');
  }
  function closeModal($modal) {
    $modal.removeClass('si-pt-open');
  }

  $(document).on('click', '.si-pt-modal-cancel', function () {
    closeModal($(this).closest('.si-pt-modal'));
  });
  $(document).on('click', '.si-pt-modal', function (e) {
    if (e.target === this) {
      closeModal($(this));
    }
  });

  // --- Create / Edit Project ---
  $(document).on('click', '.si-pt-open-add-project', function (e) {
    e.preventDefault();
    $('#si-pt-project-modal-title').html('&#128193; ' + 'Create New Project');
    $('#si-pt-project-form')[0].reset();
    $('#si-pt-project-id').val('');
    $('#si-pt-progress-out').text('0');
    openModal($('#si-pt-project-modal'));
  });

  $(document).on('click', '.si-pt-edit-project', function () {
    var data = $(this).data();
    $('#si-pt-project-modal-title').html('&#9998; ' + 'Edit Project');
    $('#si-pt-project-id').val(data.id);
    $('#si-pt-title').val(data.title);
    $('#si-pt-category').val(data.category);
    $('#si-pt-status').val(data.status);
    $('#si-pt-progress').val(data.progress);
    $('#si-pt-progress-out').text(data.progress);
    $('#si-pt-description').val(data.description);
    $('#si-pt-repo_url').val(data.repoUrl);
    $('#si-pt-live_url').val(data.liveUrl);
    $('#si-pt-target_date').val(data.targetDate);
    openModal($('#si-pt-project-modal'));
  });

  $('#si-pt-project-form').on('submit', function (e) {
    e.preventDefault();
    var formData = $(this).serialize() + '&action=si_pt_save_project&nonce=' + siPtData.nonce;
    $.post(siPtData.ajax_url, formData, function (response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.data && response.data.message ? response.data.message : 'Could not save project.');
      }
    }).fail(function () {
      alert('Request failed. Please try again.');
    });
  });

  $(document).on('click', '.si-pt-delete-project', function () {
    if (!confirm('Delete this project? This will also remove its notes and reference documents.')) {
      return;
    }
    var id = $(this).data('id');
    $.post(siPtData.ajax_url, {
      action: 'si_pt_delete_project',
      nonce: siPtData.nonce,
      id: id
    }, function (response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.data && response.data.message ? response.data.message : 'Could not delete project.');
      }
    }).fail(function () {
      alert('Request failed. Please try again.');
    });
  });

  // --- Add Developer Note ---
  $(document).on('click', '.si-pt-add-note', function () {
    var data = $(this).data();
    $('#si-pt-note-form')[0].reset();
    $('#si-pt-note-project-id').val(data.id);
    $('#si-pt-note-project-title').text(data.title);
    openModal($('#si-pt-note-modal'));
  });

  $('#si-pt-note-form').on('submit', function (e) {
    e.preventDefault();
    var formData = $(this).serialize() + '&action=si_pt_add_note&nonce=' + siPtData.nonce;
    $.post(siPtData.ajax_url, formData, function (response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.data && response.data.message ? response.data.message : 'Could not add note.');
      }
    }).fail(function () {
      alert('Request failed. Please try again.');
    });
  });

  // --- Reference Document picker modal ---
  $(document).on('click', '.si-pt-open-doc-modal', function () {
    var service = $(this).data('service');
    var $modal = $('#si-pt-doc-modal');
    $('#si-pt-add-doc-form')[0].reset();
    $('#si-pt-doc-service_type').val(service === 'custom' ? '' : service);
    setDocTab('local'); // always start on the "select local file" tab

    if (service === 'google_drive') {
      $('#si-pt-doc-modal-title').html('&#9729; Google Drive File Picker');
      $('#si-pt-doc-modal-sub').text('Select from Google Drive or attach a shareable link');
      $('#si-pt-doc-url-label').text('Google Drive Link URL');
      $('#file_url').attr('placeholder', 'https://drive.google.com/file/d/...');
      $('#si-pt-doc-submit-btn').removeClass('si-pt-btn-primary').addClass('si-pt-btn-green').text('Confirm Upload to Drive');
      updateOauthStatus(window.siPtOauthState && window.siPtOauthState.googleConnected, 'Google Drive');
    } else if (service === 'onedrive') {
      $('#si-pt-doc-modal-title').html('&#9729; OneDrive File Picker');
      $('#si-pt-doc-modal-sub').text('Select from OneDrive or attach a shareable link');
      $('#si-pt-doc-url-label').text('OneDrive Link URL');
      $('#file_url').attr('placeholder', 'https://1drv.ms/...');
      $('#si-pt-doc-submit-btn').removeClass('si-pt-btn-green').addClass('si-pt-btn-primary').text('Confirm Upload to OneDrive');
      updateOauthStatus(window.siPtOauthState && window.siPtOauthState.onedriveConnected, 'OneDrive');
    } else {
      $('#si-pt-doc-modal-title').html('&#128279; Add Custom File Link');
      $('#si-pt-doc-modal-sub').text('Attach any reference link (Dropbox, S3, internal wiki, etc.)');
      $('#si-pt-doc-url-label').text('Document URL');
      $('#file_url').attr('placeholder', 'https://...');
      $('#si-pt-doc-submit-btn').removeClass('si-pt-btn-green').addClass('si-pt-btn-primary').text('Save Reference Link');
      $('#si-pt-doc-oauth-status').hide();
    }

    openModal($modal);
  });

  function updateOauthStatus(connected, label) {
    var $status = $('#si-pt-doc-oauth-status');
    var $hint = $('#si-pt-doc-oauth-hint');
    $status.show();
    if (connected) {
      $status.find('.si-pt-oauth-dot').css('background', '#16a34a');
      $('#si-pt-doc-oauth-text').text(label + ' API key configured');
      $hint.text('');
    } else {
      $status.find('.si-pt-oauth-dot').css('background', '#d97706');
      $('#si-pt-doc-oauth-text').text(label + ' API key not set — paste a share link below');
      if (window.siPtOauthState) {
        $hint.html('No ' + label + ' app is connected yet. Paste a share link, or add API credentials in <a href="' + window.siPtOauthState.settingsUrl + '">Plugin Settings</a>.');
      }
    }
  }

  // Tab switching: "Select Local File from Desktop" shows the drag & drop
  // box; "Paste Existing Share Link" hides it and just leaves the URL
  // field (previously these tabs were purely decorative and did nothing
  // when clicked).
  function setDocTab(tab) {
    var $localTab = $('#si-pt-doc-tab-local');
    var $presetsTab = $('#si-pt-doc-tab-presets');
    var $localPanel = $('#si-pt-doc-local-panel');

    if (tab === 'presets') {
      $localTab.removeClass('active');
      $presetsTab.addClass('active');
      $localPanel.slideUp(120);
      $('#file_url').trigger('focus');
    } else {
      $presetsTab.removeClass('active');
      $localTab.addClass('active');
      $localPanel.slideDown(120);
    }
  }

  $(document).on('click', '#si-pt-doc-tab-local', function () {
    setDocTab('local');
  });
  $(document).on('click', '#si-pt-doc-tab-presets', function () {
    setDocTab('presets');
  });

  $(document).on('click', '#si-pt-doc-dropzone', function () {
    $('#si-pt-doc-file-input').trigger('click');
  });
  $(document).on('change', '#si-pt-doc-file-input', function () {
    if (this.files && this.files[0]) {
      $('#file_name').val(this.files[0].name);
    }
  });

  $('#si-pt-add-doc-form').on('submit', function (e) {
    e.preventDefault();
    var formData = $(this).serialize() + '&action=si_pt_add_doc&nonce=' + siPtData.nonce;
    $.post(siPtData.ajax_url, formData, function (response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.data && response.data.message ? response.data.message : 'Could not save this reference document.');
      }
    }).fail(function () {
      alert('Request failed. Please try again.');
    });
  });

  $(document).on('click', '.si-pt-delete-doc', function () {
    if (!confirm('Delete this reference document?')) {
      return;
    }
    var docId = $(this).data('id');
    $.post(siPtData.ajax_url, {
      action: 'si_pt_delete_doc',
      nonce: siPtData.nonce,
      doc_id: docId
    }, function (response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.data && response.data.message ? response.data.message : 'Could not delete document.');
      }
    }).fail(function () {
      alert('Request failed. Please try again.');
    });
  });

  // --- Projects List: search + filter ---
  function applyProjectFilters() {
    var q = ($('#si-pt-project-search').val() || '').toLowerCase();
    var cat = $('#si-pt-filter-category').val();
    var status = $('#si-pt-filter-status').val();
    var visibleCount = 0;

    $('#si-pt-project-card-grid .si-pt-proj-card').each(function () {
      var $card = $(this);
      var matchesText = !q || $card.data('title').toString().indexOf(q) !== -1;
      var matchesCat = !cat || $card.data('category') === cat;
      var matchesStatus = !status || $card.data('status') === status;
      var visible = matchesText && matchesCat && matchesStatus;
      $card.toggle(visible);
      if (visible) visibleCount++;
    });

    $('#si-pt-project-no-results').toggle(visibleCount === 0 && $('#si-pt-project-card-grid .si-pt-proj-card').length > 0);
  }

  $('#si-pt-project-search, #si-pt-filter-category, #si-pt-filter-status').on('input change', applyProjectFilters);

  // --- Dashboard: category filter pills on Projects Overview list ---
  $('#si-pt-dash-filter-pills').on('click', '.si-pt-filter-pill', function () {
    var cat = $(this).data('cat');
    $('#si-pt-dash-filter-pills .si-pt-filter-pill').removeClass('active');
    $(this).addClass('active');

    $('#si-pt-dash-project-list .si-pt-project-row').each(function () {
      var $row = $(this);
      var visible = cat === 'all' || $row.data('category') === cat;
      $row.toggle(visible);
    });
  });

  // --- Reminders ---
  $(document).on('click', '#si-pt-open-add-reminder', function () {
    $('#si-pt-reminder-form')[0].reset();
    $('#si-pt-reminder-project').val('');
    openModal($('#si-pt-reminder-modal'));
  });

  $(document).on('click', '.si-pt-open-add-reminder-for', function () {
    var projectId = $(this).data('project-id');
    $('#si-pt-reminder-form')[0].reset();
    $('#si-pt-reminder-project').val(projectId);
    openModal($('#si-pt-reminder-modal'));
  });

  $('#si-pt-reminder-form').on('submit', function (e) {
    e.preventDefault();
    var formData = $(this).serialize() + '&action=si_pt_save_reminder&nonce=' + siPtData.nonce;
    $.post(siPtData.ajax_url, formData, function (response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.data && response.data.message ? response.data.message : 'Could not schedule reminder.');
      }
    }).fail(function () {
      alert('Request failed. Please try again.');
    });
  });

  $(document).on('click', '.si-pt-delete-reminder', function () {
    if (!confirm('Delete this reminder?')) {
      return;
    }
    var id = $(this).data('id');
    $.post(siPtData.ajax_url, {
      action: 'si_pt_delete_reminder',
      nonce: siPtData.nonce,
      id: id
    }, function (response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.data && response.data.message ? response.data.message : 'Could not delete reminder.');
      }
    }).fail(function () {
      alert('Request failed. Please try again.');
    });
  });

  // --- Settings: Custom Categories ---
  $('#si-pt-add-category-btn').on('click', function () {
    var name = $.trim($('#si-pt-new-category-name').val());
    if (!name) {
      return;
    }
    $.post(siPtData.ajax_url, {
      action: 'si_pt_add_category',
      nonce: siPtData.nonce,
      name: name
    }, function (response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.data && response.data.message ? response.data.message : 'Could not add category.');
      }
    }).fail(function () {
      alert('Request failed. Please try again.');
    });
  });

  $(document).on('click', '.si-pt-delete-category', function () {
    if (!confirm('Remove this category? Existing projects keep it, but it will no longer be offered for new ones.')) {
      return;
    }
    var name = $(this).data('name');
    $.post(siPtData.ajax_url, {
      action: 'si_pt_delete_category',
      nonce: siPtData.nonce,
      name: name
    }, function (response) {
      if (response.success) {
        location.reload();
      } else {
        alert(response.data && response.data.message ? response.data.message : 'Could not remove category.');
      }
    }).fail(function () {
      alert('Request failed. Please try again.');
    });
  });

  // --- Settings: Export / Import backup ---
  $('#si-pt-export-btn').on('click', function () {
    var $btn = $(this).prop('disabled', true).text('Preparing export...');
    $.post(siPtData.ajax_url, {
      action: 'si_pt_export_data',
      nonce: siPtData.nonce
    }, function (response) {
      if (response.success) {
        var blob = new Blob([JSON.stringify(response.data, null, 2)], { type: 'application/json' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        var stamp = new Date().toISOString().slice(0, 10);
        a.href = url;
        a.download = 'si-project-tracker-backup-' + stamp + '.json';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      } else {
        alert(response.data && response.data.message ? response.data.message : 'Export failed.');
      }
    }).fail(function () {
      alert('Request failed. Please try again.');
    }).always(function () {
      $btn.prop('disabled', false).html('&#11015; Export Total Data Backup (JSON)');
    });
  });

  $('#si-pt-import-dropzone').on('click', function () {
    $('#si-pt-import-file-input').trigger('click');
  });

  $('#si-pt-import-file-input').on('change', function () {
    var file = this.files && this.files[0];
    if (!file) return;

    if (!confirm('Import "' + file.name + '"? This will add its projects, notes, and documents as new records.')) {
      $(this).val('');
      return;
    }

    var reader = new FileReader();
    reader.onload = function (e) {
      $.post(siPtData.ajax_url, {
        action: 'si_pt_import_data',
        nonce: siPtData.nonce,
        payload: e.target.result
      }, function (response) {
        if (response.success) {
          alert(response.data.message);
          location.reload();
        } else {
          alert(response.data && response.data.message ? response.data.message : 'Import failed.');
        }
      }).fail(function () {
        alert('Request failed. Please try again.');
      });
    };
    reader.readAsText(file);
  });

});
