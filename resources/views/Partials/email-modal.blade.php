<!-- Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="emailModalLabel">
                    <i class="fas fa-envelope mr-2"></i>Compose Email
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="emailForm">
                @csrf
                <input type="hidden" name="entity_type" id="email_entity_type">
                <input type="hidden" name="entity_id" id="email_entity_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label mb-1">To</label>
                        <input type="email" name="to_email" id="email_to" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label mb-1">Subject</label>
                        <input type="text" name="subject" id="email_subject" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label mb-1">Message</label>
                        <textarea name="body" id="email_body" rows="8" class="form-control" required placeholder="Write your message here..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label mb-1">CC (Optional)</label>
                                <input type="text" name="cc" id="email_cc" class="form-control" placeholder="cc@example.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label mb-1">BCC (Optional)</label>
                                <input type="text" name="bcc" id="email_bcc" class="form-control" placeholder="bcc@example.com">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-paper-plane mr-1"></i> Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#emailForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("admin.email.send") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#emailModal').modal('hide');
                    $('#emailForm')[0].reset();
                    
                    // Reload emails list if on the page
                    if(typeof loadEmails === 'function') {
                        loadEmails();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong!'
                });
            }
        });
    });
});

function openEmailModal(entityType, entityId, email) {
    $('#email_entity_type').val(entityType);
    $('#email_entity_id').val(entityId);
    $('#email_to').val(email);
    $('#email_subject').val('');
    $('#email_body').val('');
    $('#email_cc').val('');
    $('#email_bcc').val('');
    $('#emailModal').modal('show');
}
</script>