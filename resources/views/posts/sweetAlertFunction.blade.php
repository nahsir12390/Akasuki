
<script>
    
    function confirmDelete(postId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to undo this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById(`delete-form-${postId}`);
                if (form) {
                    form.submit(); // Submits the form
                } else {
                    console.error("Form not found for post ID:", postId);
                }
            }
        });
    }
</script>
