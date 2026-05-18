<div class="modal fade" id="termResultModal" tabindex="-1" aria-labelledby="termResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termResultModalLabel">Select Term Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-muted small mb-3" id="termResultStudentName"></div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#" target="_blank" class="btn btn-primary d-none" id="termOneResultLink">
                        First Term Result
                    </a>
                    <a href="#" target="_blank" class="btn btn-primary d-none" id="termTwoResultLink">
                        Second Term Result
                    </a>
                </div>
                <div class="alert alert-warning mb-0 d-none" id="termResultEmpty">
                    No term marks are available for this result yet.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function(event) {
        const button = event.target.closest('.term-result-btn');
        if (!button) return;

        const termOneLink = document.getElementById('termOneResultLink');
        const termTwoLink = document.getElementById('termTwoResultLink');
        const emptyState = document.getElementById('termResultEmpty');
        const studentName = document.getElementById('termResultStudentName');
        const hasTermOne = button.dataset.hasTermOne === '1';
        const hasTermTwo = button.dataset.hasTermTwo === '1';

        studentName.textContent = button.dataset.student || '';
        termOneLink.href = button.dataset.termOneUrl || '#';
        termTwoLink.href = button.dataset.termTwoUrl || '#';
        termOneLink.classList.toggle('d-none', !hasTermOne);
        termTwoLink.classList.toggle('d-none', !hasTermTwo);
        emptyState.classList.toggle('d-none', hasTermOne || hasTermTwo);

        const modal = new bootstrap.Modal(document.getElementById('termResultModal'));
        modal.show();
    });
</script>
