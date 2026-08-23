<style>
    .erp-tree {
        list-style: none;
        padding-left: 18px;
        margin: 0;
        position: relative;
        color: var(--erp-ink, #1c1410);
    }

    .erp-tree.erp-tree--root {
        padding-left: 0;
    }

    .erp-tree:not(.erp-tree--root):before {
        content: "";
        width: 2px;
        background: var(--erp-accent-soft, #e7f2ec);
        top: 0;
        bottom: 4px;
        left: 0;
        position: absolute;
    }

    .erp-tree-node {
        position: relative;
        margin: 2px 0;
    }

    .erp-tree:not(.erp-tree--root) > .erp-tree-node:before {
        content: "";
        width: 14px;
        height: 1px;
        background: var(--erp-line, #e6e0d8);
        position: absolute;
        top: 16px;
        left: -18px;
    }

    .erp-tree-label {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        line-height: 1.3;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .erp-tree-label:hover {
        background: var(--erp-accent-soft, #e7f2ec);
        color: var(--erp-accent-deep, #245540);
    }

    .erp-tree-label.is-selected {
        background: var(--erp-accent, #2f6b4f);
        color: #fff;
    }

    .erp-tree-label.is-selected .erp-tree-type {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    .erp-tree-label .fa {
        width: 16px;
        text-align: center;
        margin: 0;
        padding: 0;
        opacity: 0.85;
    }

    .erp-tree-type {
        margin-left: auto;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--erp-muted, #6b625b);
        background: #f0ebe4;
        padding: 2px 6px;
        border-radius: 999px;
    }

    .erp-tree-label.is-ledger .erp-tree-name {
        font-weight: 500;
    }

    .erp-tree .erp-tree {
        display: none;
        margin-top: 2px;
    }
</style>

<ul class="erp-tree erp-tree--root">
    @if (isset($allChartOfAccounts) && count($allChartOfAccounts))
        @foreach ($allChartOfAccounts as $row)
            <li class="erp-tree-node" data-name="{{ strtolower($row->name) }}">
                <span class="erp-tree-label branch {{ $row->type === 'ledger' ? 'is-ledger' : '' }}"
                      data-id="{{ $row->id }}"
                      onclick="changeChart({{ $row->id }})">
                    <i class="fa {{ $row->type === 'group' ? 'fa-folder' : 'fa-file-text-o' }}"></i>
                    <span class="erp-tree-name">{{ $row->name }}</span>
                    <span class="erp-tree-type">{{ $row->type }}</span>
                </span>
                @if ($row->childrens && count($row->childrens))
                    @include('chart_of_accounts.sub-group-list', ['subcharts' => $row->childrens])
                @endif
            </li>
        @endforeach
    @else
        <li class="erp-tree-node">
            <div class="erp-chart__loading">No accounts found.</div>
        </li>
    @endif
</ul>

<script>
    (function () {
        $('#inventoryItems').off('click.coaTree', '.branch').on('click.coaTree', '.branch', function (e) {
            const $branch = $(this);
            const $tree = $branch.next('.erp-tree');
            if (!$tree.length) {
                return;
            }
            e.stopPropagation();
            $branch.find('> .fa').toggleClass('fa-folder-open');
            $tree.slideToggle(120);
        });
    })();
</script>
