// VPS (QEMU) server creation form. A trimmed counterpart to new-server.js with
// no nest/egg/service-variable handling — a VPS is built from a Pup template,
// so the form only needs node -> allocation wiring, the Pup picker and the
// owner select.
$(document).ready(function () {
    $('#pNodeId').select2({
        placeholder: 'Select a Node',
    }).change();

    $('#pAllocation').select2({
        placeholder: 'Select a Default Allocation',
    });

    $('#pAllocationAdditional').select2({
        placeholder: 'Select Additional Allocations',
    });

    $('#pPupId').select2({
        placeholder: 'Select a VPS Template',
    });
});

let lastActiveBox = null;
$(document).on('click', function (event) {
    if (lastActiveBox !== null) {
        lastActiveBox.removeClass('box-primary');
    }

    lastActiveBox = $(event.target).closest('.box');
    lastActiveBox.addClass('box-primary');
});

$('#pNodeId').on('change', function () {
    const currentNode = $(this).val();
    $.each(Ruff.nodeData, function (i, v) {
        if (v.id == currentNode) {
            $('#pAllocation').html('').select2({
                data: v.allocations,
                placeholder: 'Select a Default Allocation',
            });

            updateAdditionalAllocations();
        }
    });
});

$('#pAllocation').on('change', function () {
    updateAdditionalAllocations();
});

function updateAdditionalAllocations() {
    const currentAllocation = $('#pAllocation').val();
    const currentNode = $('#pNodeId').val();

    $.each(Ruff.nodeData, function (i, v) {
        if (v.id == currentNode) {
            const allocations = [];

            for (let i = 0; i < v.allocations.length; i++) {
                const allocation = v.allocations[i];

                if (allocation.id != currentAllocation) {
                    allocations.push(allocation);
                }
            }

            $('#pAllocationAdditional').html('').select2({
                data: allocations,
                placeholder: 'Select Additional Allocations',
            });
        }
    });
}

function initUserIdSelect(data) {
    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    $('#pUserId').select2({
        ajax: {
            url: '/admin/users/accounts.json',
            dataType: 'json',
            delay: 250,

            data: function (params) {
                return {
                    filter: { email: params.term },
                    page: params.page,
                };
            },

            processResults: function (data, params) {
                return { results: data };
            },

            cache: true,
        },

        data: data,
        escapeMarkup: function (markup) { return markup; },
        minimumInputLength: 2,
        templateResult: function (data) {
            if (data.loading) return escapeHtml(data.text);

            return '<div class="user-block"> \
                <img class="img-circle img-bordered-xs" src="https://www.gravatar.com/avatar/' + escapeHtml(data.md5) + '?s=120" alt="User Image"> \
                <span class="username"> \
                    <a href="#">' + escapeHtml(data.name_first) + ' ' + escapeHtml(data.name_last) +'</a> \
                </span> \
                <span class="description"><strong>' + escapeHtml(data.email) + '</strong> - ' + escapeHtml(data.username) + '</span> \
            </div>';
        },
        templateSelection: function (data) {
            return '<div> \
                <span> \
                    <img class="img-rounded img-bordered-xs" src="https://www.gravatar.com/avatar/' + escapeHtml(data.md5) + '?s=120" style="height:28px;margin-top:-4px;" alt="User Image"> \
                </span> \
                <span style="padding-left:5px;"> \
                    ' + escapeHtml(data.name_first) + ' ' + escapeHtml(data.name_last) + ' (<strong>' + escapeHtml(data.email) + '</strong>) \
                </span> \
            </div>';
        }

    });
}
