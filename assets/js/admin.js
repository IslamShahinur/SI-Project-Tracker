/* SI Project Tracker - Admin JavaScript */
jQuery(document).ready(function ($) {

  function showModal($modal) {
    $modal.css('display', 'flex');
  }

  function hideModal($modal) {
    $modal.css('display', 'none');
  }

  $(document).on('click', '.si-pt-modal-cancel', function () {
    hideModal($(this).closest('.si-pt-modal'));
  });

  // Clicking the dark overlay (outside the modal content) closes it.
  $(document).on('click', '.si-pt-modal', function (e) {
    if (e.target === this) {
      hideModal($(this));
    }
  });

  // --- Attach Reference Document ---
  $('#si-pt-add-doc-form').on('submit', function (e) {
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serialize() + '&action=si_pt_add_doc&nonce=' + siPtData.nonce;
    $.post(siPtData.ajax_url, formData, function (response) {
      if (response.success) {
        alert(response.data.message);
        location.reload();
      } else {
        alert(response.data && response.data.message ? response.data.message : 'Something went wrong.');
      }
    }).fail(function () {
      alert('Request failed. Please try again.');
    });
  });

  // --- Delete Reference Document ---
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

  // --- Add New Project ---
  $('#si-pt-open-add-project').on('click', function () {
    var $modal = $('#si-pt-project-modal');
    $('#si-pt-project-modal-title').text('Add New Project');
    $('#si-pt-project-form')[0].reset();
    $('#si-pt-project-id').val('');
    showModal($modal);
  });

  // --- Edit Existing Project ---
  $(document).on('click', '.si-pt-edit-project', function () {
    var data = $(this).data();
    var $modal = $('#si-pt-project-modal');

    $('#si-pt-project-modal-title').text('Edit Project');
    $('#si-pt-project-id').val(data.id);
    $('#si-pt-title').val(data.title);
    $('#si-pt-category').val(data.category);
    $('#si-pt-status').val(data.status);
    $('#si-pt-progress').val(data.progress);
    $('#si-pt-description').val(data.description);
    $('#si-pt-repo_url').val(data.repoUrl);
    $('#si-pt-live_url').val(data.liveUrl);
    $('#si-pt-target_date').val(data.targetDate);

    showModal($modal);
  });

  // --- Save Project (Create or Update) ---
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

  // --- Delete Project ---
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
    showModal($('#si-pt-note-modal'));
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

  // --- Custom Categories (Settings page) ---
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

  // --- Reminders (classic list page has its own inline form; the
  // "Set Reminder" links on the Projects List page just navigate there
  // with the project pre-selected, no modal needed here) ---
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

});
