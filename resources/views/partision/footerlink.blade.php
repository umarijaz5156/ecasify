@php
    use App\Models\Utility;
@endphp
<footer class="dash-footer">
    <div class="footer-wrapper">
        <div class="py-1">
            <span
                class="text-muted">{{ Utility::getValByName('footer_text') ? Utility::getValByName('footer_text') : __('© Copyright Ecasify') }}
                {{ date('Y') }}</span>
        </div>
    </div>
</footer>


<!-- Required Js -->
<script type src="{{ asset('js/jquery.js') }}"></script>
<script src="{{ asset('assets/js/plugins/popper.js') }}"></script>
<script src="{{ asset('assets/js/plugins/simplebar.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-switch-button.js') }}"></script>
<script src="{{ asset('assets/js/dash.js') }}"></script>
<script src="{{ asset('assets/js/plugins/apexcharts.js') }}"></script>
<script src="{{ asset('assets/js/plugins/notifier.js') }}"></script>
<script src="{{ asset('assets/js/plugins/sweetalert2.all.js') }}"></script>
<script src="{{ asset('assets/js/plugins/choices.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.3/plugins/minMaxTimePlugin.js">
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="{{ asset('assets/js/plugins/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/js/plugins/dataTables.bootstrap5.js') }}"></script>

<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
<script src="{{ asset('assets/js/plugins/tinymce/tinymcenew.js') }}"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

{{-- daternage --}}
<script  src="{{ asset('assets/plugins/daterangepicker-master/moment.min.js') }}"></script>
<script  src="{{ asset('assets/plugins/daterangepicker-master/daterangepicker.js') }}"></script>


<script>

$(function () {
      $('input[name="daterange"]').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        locale: {
          format: "MM/DD/YYYY h:mm A",
        },
      });
    });


  $('.dataTable').each(function() {
    if (! $.fn.DataTable.isDataTable(this)) {
      $(this).DataTable({
        order: [],
      });
    }
  });


    feather.replace();
    var pctoggle = document.querySelector("#pct-toggler");
    if (pctoggle) {
        pctoggle.addEventListener("click", function() {
            if (
                !document.querySelector(".pct-customizer").classList.contains("active")
            ) {
                document.querySelector(".pct-customizer").classList.add("active");
            } else {
                document.querySelector(".pct-customizer").classList.remove("active");
            }
        });
    }

    var themescolors = document.querySelectorAll(".themes-color > a");
    for (var h = 0; h < themescolors.length; h++) {
        var c = themescolors[h];

        c.addEventListener("click", function(event) {
            var targetElement = event.target;
            if (targetElement.tagName == "SPAN") {
                targetElement = targetElement.parentNode;
            }
            var temp = targetElement.getAttribute("data-value");
            removeClassByPrefix(document.querySelector("body"), "theme-");
            document.querySelector("body").classList.add(temp);
        });
    }

    function removeClassByPrefix(node, prefix) {
        for (let i = 0; i < node.classList.length; i++) {
            let value = node.classList[i];
            if (value.startsWith(prefix)) {
                node.classList.remove(value);
            }
        }
    }
</script>
