<!-- jQuery FIRST -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dropify/dist/js/dropify.min.js"></script>

<!-- Bootstrap JS -->
<script src="{{ asset('Backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- Other plugins -->
<script src="{{ asset('Backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('Backend/assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('Backend/assets/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('Backend/assets/js/plugins.js') }}"></script>

<!-- App JS -->
<script src="{{ asset('Backend/assets/js/app.js') }}"></script>
<script src="{{ asset('Backend/assets/js/layout.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Dropify initialization + remove flag handling
        if (window.jQuery && jQuery().dropify) {
            const drs = $('.dropify').dropify();
            drs.on('dropify.afterClear', function(event, element) {
                const input = element.element;
                const wrapper = input.closest('.form-field-wrapper');
                if (wrapper) {
                    const flag = wrapper.querySelector('[data-remove-flag]');
                    if (flag) flag.value = '1';
                }
            });
            drs.on('change', function(event) {
                const input = event.target;
                const wrapper = input.closest('.form-field-wrapper');
                if (wrapper) {
                    const flag = wrapper.querySelector('[data-remove-flag]');
                    if (flag) flag.value = '0';
                }
            });
        }

        // Sidebar toggle: force clean collapse/expand behavior on vertical/semibox layouts.
        const oldBtn = document.getElementById("topnav-hamburger-icon");
        if (oldBtn) {
            const newBtn = oldBtn.cloneNode(true);
            oldBtn.parentNode.replaceChild(newBtn, oldBtn);

            newBtn.addEventListener("click", function(e) {
                e.preventDefault();

                const html = document.documentElement;
                const layout = html.getAttribute("data-layout");
                if (layout === "vertical" || layout === "semibox") {
                    const current = html.getAttribute("data-sidebar-size") || "lg";
                    const next = current === "sm" ? "lg" : "sm";
                    html.setAttribute("data-sidebar-size", next);
                    sessionStorage.setItem("data-sidebar-size", next);
                    document.body.classList.remove("vertical-sidebar-enable");

                    const icon = newBtn.querySelector(".hamburger-icon");
                    if (icon) {
                        icon.classList.toggle("open", next === "sm");
                    }
                    return;
                }

                if (layout === "horizontal") {
                    document.body.classList.toggle("menu");
                    return;
                }

                if (layout === "twocolumn") {
                    document.body.classList.toggle("twocolumn-panel");
                    return;
                }
            });
        }

        // Light/Dark mode: keep icon in sync and persist preference.
        let themeBtns = Array.from(document.querySelectorAll(".light-dark-mode"));
        if (themeBtns.length) {
            themeBtns = themeBtns.map((btn) => {
                const clone = btn.cloneNode(true);
                btn.parentNode.replaceChild(clone, btn);
                return clone;
            });
        }
        const updateThemeIcon = (theme) => {
            themeBtns.forEach((btn) => {
                const icon = btn.querySelector("i");
                if (!icon) return;
                if (theme === "dark") {
                    icon.classList.remove("bx-moon");
                    icon.classList.add("bx-sun");
                } else {
                    icon.classList.remove("bx-sun");
                    icon.classList.add("bx-moon");
                }
            });
        };

        if (themeBtns.length) {
            themeBtns.forEach((btn) => {
                btn.addEventListener("click", function() {
                    const html = document.documentElement;
                    const current = html.getAttribute("data-bs-theme") || "light";
                    const next = current === "dark" ? "light" : "dark";
                    html.setAttribute("data-bs-theme", next);
                    sessionStorage.setItem("data-bs-theme", next);
                    updateThemeIcon(next);

                    const lightRadio = document.getElementById("layout-mode-light");
                    const darkRadio = document.getElementById("layout-mode-dark");
                    if (lightRadio && darkRadio) {
                        lightRadio.checked = next === "light";
                        darkRadio.checked = next === "dark";
                    }
                });
            });

            // Initialize icon based on saved theme.
            const savedTheme = sessionStorage.getItem("data-bs-theme") ||
                document.documentElement.getAttribute("data-bs-theme") || "light";
            updateThemeIcon(savedTheme);

            const lightRadio = document.getElementById("layout-mode-light");
            const darkRadio = document.getElementById("layout-mode-dark");
            if (lightRadio && darkRadio) {
                lightRadio.checked = savedTheme === "light";
                darkRadio.checked = savedTheme === "dark";
            }
        }
    });
</script>
{{-- date --}}
<script type="text/javascript">
    setInterval(function() {
        var currentTime = new Date();
        var currentHours = currentTime.getHours();
        var currentMinutes = currentTime.getMinutes();
        var currentSeconds = currentTime.getSeconds();
        currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
        currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
        var timeOfDay = currentHours < 12 ? "AM" : "PM";
        currentHours = currentHours > 12 ? currentHours - 12 : currentHours;
        currentHours = currentHours == 0 ? 12 : currentHours;
        var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
        document.getElementById("timer").innerHTML = currentTimeString;
    }, 1000);
</script>
{{-- bulk single and multiple --}}
<script>
    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })
    // bulk checked
    function select_all() {
        if ($('#select_all:checked').length == 1) {
            $('.select_data').prop('checked', true);
            if ($('.select_data:checked').length >= 1) {
                $('.delete_btn').removeClass('d-none');
            }
        } else {
            $('.select_data').prop('checked', false);
            $('.delete_btn').addClass('d-none');
        }
    }

    // single checkbox
    function select_single_item(id) {
        var total = $('.select_data').length; //count total checkbox
        var total_checked = $('.select_data:checked').length; //count total checked checkbox
        (total == total_checked) ? $('#select_all').prop('checked', true): $('#select_all').prop('checked', false);
        (total_checked > 0) ? $('.delete_btn').removeClass('d-none'): $('.delete_btn').addClass('d-none');
    }


    // multi delete
    function bulk_delete(ids, url, rows, table) {
        Swal.fire({
            title: 'Are you sure to delete all checked data?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Confirm',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        ids: ids,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "JSON",
                }).done(function(response) {
                    if (response.success) {
                        Swal.fire("Deleted", response.message, "success").then(function() {
                            table.rows(rows).remove().draw(false);
                            $('#select_all').prop('checked', false);
                            $('.delete_btn').addClass('d-none');
                            table.DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Oops...', response.message, "error");
                    }
                }).fail(function() {
                    Swal.fire('Oops...', "Somthing went wrong with ajax!", "error");
                });
            }
        });
    }
</script>
{{-- toster notification --}}
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })

    @if (session('success'))
        Toast.fire({
            icon: 'success',
            title: '{{ session('success') }}',
        })
    @endif

    @if (session('error'))
        alert('yo');
        Toast.fire({
            icon: 'error',
            title: '{{ session('error') }}',
        })
    @endif

    $(".del").click(function() {
        let url = $(this).attr("name")
        deleteData(url);
    })

    function deleteData(url) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                var link = url;
                window.location.href = link;
            }
        })
    }
</script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
