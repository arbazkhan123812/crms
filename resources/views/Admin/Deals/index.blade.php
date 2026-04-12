@extends('layout.admin')

@section('content')
<div class="content deal-board-page">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-7">
                <h3 class="page-title text-dark mb-2">
                    <i class="fas fa-handshake text-primary mr-2"></i>Deals Pipeline
                </h3>
                <p class="text-muted mb-0">Track every deal by stage, move cards across the pipeline, and keep the sales flow visually organized.</p>
            </div>
            <div class="col-lg-4 col-md-5 text-md-right mt-3 mt-md-0">
                <a href="{{ route('admin.deals.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle mr-1"></i> Create New Deal
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="deal-board">
        @foreach($stages as $stage)
            @php
                $stageDeals = $deals->where('stage', $stage);
            @endphp
            <div class="deal-column" data-stage="{{ $stage }}">
                <div class="deal-column-header">
                    <div>
                        <h5 class="mb-1">{{ $stage }}</h5>
                        <small>{{ $stageDeals->count() }} {{ \Illuminate\Support\Str::plural('deal', $stageDeals->count()) }}</small>
                    </div>
                    <span class="deal-column-chip">{{ $stageDeals->sum('amount') > 0 ? 'PKR ' . number_format($stageDeals->sum('amount'), 0) : 'No value' }}</span>
                </div>

                <div class="deal-dropzone" data-stage="{{ $stage }}">
                    @forelse($stageDeals as $deal)
                        <div class="deal-card" draggable="true" data-deal-id="{{ $deal->id }}" data-amount="{{ (float) $deal->amount }}">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="deal-card-title">{{ optional($deal->account)->name ?: 'No Account' }}</div>
                                    <div class="deal-card-subtitle">{{ optional($deal->contact)->full_name ?: 'No Contact' }}</div>
                                </div>
                                <span class="deal-probability">{{ $deal->probability }}%</span>
                            </div>

                            <div class="deal-meta-row">
                                <span class="deal-meta-label">Stage</span>
                                <span>{{ $deal->stage }}</span>
                            </div>
                            <div class="deal-meta-row">
                                <span class="deal-meta-label">Amount</span>
                                <span>PKR {{ number_format((float) $deal->amount, 2) }}</span>
                            </div>
                            <div class="deal-meta-row">
                                <span class="deal-meta-label">Closing</span>
                                <span>{{ optional($deal->closing_date)->format('d M Y') ?: 'Not set' }}</span>
                            </div>
                            <div class="deal-meta-row border-0 pb-0 mb-0">
                                <span class="deal-meta-label">Owner</span>
                                <span>{{ optional($deal->owner)->username ?: '-' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="deal-empty-state">Drop deals here</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    (function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let draggedCard = null;

        const formatCurrency = (amount) => {
            const numericAmount = Number(amount || 0);

            if (numericAmount <= 0) {
                return 'No value';
            }

            return 'PKR ' + numericAmount.toLocaleString('en-US', {
                maximumFractionDigits: 0
            });
        };

        const updateColumnSummary = (zone) => {
            const column = zone.closest('.deal-column');
            const cards = zone.querySelectorAll('.deal-card');
            const dealCount = cards.length;
            const amountTotal = Array.from(cards).reduce((total, card) => {
                return total + Number(card.dataset.amount || 0);
            }, 0);

            column.querySelector('.deal-column-header small').textContent = `${dealCount} ${dealCount === 1 ? 'deal' : 'deals'}`;
            column.querySelector('.deal-column-chip').textContent = formatCurrency(amountTotal);
        };

        const syncEmptyState = (zone) => {
            const cards = zone.querySelectorAll('.deal-card');
            const emptyState = zone.querySelector('.deal-empty-state');

            if (cards.length === 0) {
                if (!emptyState) {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'deal-empty-state';
                    placeholder.textContent = 'Drop deals here';
                    zone.appendChild(placeholder);
                }

                return;
            }

            if (emptyState) {
                emptyState.remove();
            }
        };

        document.querySelectorAll('.deal-card').forEach((card) => {
            card.addEventListener('dragstart', function () {
                draggedCard = this;
                this.classList.add('dragging');
            });

            card.addEventListener('dragend', function () {
                this.classList.remove('dragging');
                draggedCard = null;
            });
        });

        document.querySelectorAll('.deal-dropzone').forEach((zone) => {
            zone.addEventListener('dragover', function (event) {
                event.preventDefault();
                this.classList.add('is-over');
            });

            zone.addEventListener('dragleave', function () {
                this.classList.remove('is-over');
            });

            zone.addEventListener('drop', function (event) {
                event.preventDefault();
                this.classList.remove('is-over');

                if (!draggedCard) {
                    return;
                }

                const newStage = this.dataset.stage;
                const movedCard = draggedCard;
                const sourceZone = movedCard.closest('.deal-dropzone');
                const dealId = movedCard.dataset.dealId;
                const currentStage = sourceZone.dataset.stage;

                if (newStage === currentStage) {
                    return;
                }

                const zone = this;

                $.ajax({
                    url: '{{ url("admin/deals") }}/' + dealId + '/stage',
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        _method: 'PUT',
                        stage: newStage
                    },
                    success: function (response) {
                        if (response.success) {
                            zone.appendChild(movedCard);
                            movedCard.dataset.stage = newStage;
                            movedCard.querySelector('.deal-meta-row span:last-child').textContent = newStage;
                            syncEmptyState(sourceZone);
                            syncEmptyState(zone);
                            updateColumnSummary(sourceZone);
                            updateColumnSummary(zone);
                            Swal.fire({
                                icon: 'success',
                                title: 'Stage Updated',
                                text: response.message,
                                timer: 1300,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Unable to update deal stage.' });
                        }
                    },
                    error: function (xhr) {
                        const message = xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Unable to update deal stage.';
                        Swal.fire({ icon: 'error', title: 'Error', text: message });
                    }
                });
            });
        });

        document.querySelectorAll('.deal-dropzone').forEach((zone) => {
            syncEmptyState(zone);
            updateColumnSummary(zone);
        });
    })();
</script>

<style>
    .deal-board-page {
        --deal-bg: linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
        --deal-border: #d6e3f0;
        --deal-text: #10243e;
        --deal-muted: #66768b;
        --deal-accent: #2f80ed;
        --deal-accent-soft: rgba(47, 128, 237, 0.12);
    }

    .deal-board {
        display: grid;
        grid-template-columns: repeat(6, minmax(260px, 1fr));
        gap: 1rem;
        overflow-x: auto;
        padding-bottom: 1rem;
    }

    .deal-column {
        min-width: 260px;
        background: var(--deal-bg);
        border: 1px solid var(--deal-border);
        border-radius: 20px;
        padding: 1rem;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
    }

    .deal-column-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: .75rem;
        margin-bottom: 1rem;
    }

    .deal-column-header h5 {
        font-size: 1rem;
        color: var(--deal-text);
    }

    .deal-column-header small {
        color: var(--deal-muted);
    }

    .deal-column-chip {
        white-space: nowrap;
        padding: .4rem .7rem;
        border-radius: 999px;
        background: #fff;
        color: var(--deal-accent);
        font-size: .72rem;
        font-weight: 700;
        border: 1px solid rgba(47, 128, 237, 0.16);
    }

    .deal-dropzone {
        min-height: 320px;
        display: flex;
        flex-direction: column;
        gap: .9rem;
        padding: .2rem;
        border-radius: 16px;
        transition: background .2s ease, border-color .2s ease;
    }

    .deal-dropzone.is-over {
        background: rgba(47, 128, 237, 0.08);
    }

    .deal-card {
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 18px;
        padding: 1rem;
        cursor: grab;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .deal-card.dragging {
        opacity: .65;
        transform: rotate(1deg);
    }

    .deal-card-title {
        color: var(--deal-text);
        font-size: .98rem;
        font-weight: 700;
    }

    .deal-card-subtitle {
        color: var(--deal-muted);
        font-size: .82rem;
        margin-top: .15rem;
    }

    .deal-probability {
        padding: .35rem .55rem;
        border-radius: 10px;
        background: var(--deal-accent-soft);
        color: var(--deal-accent);
        font-weight: 700;
        font-size: .78rem;
    }

    .deal-meta-row {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        padding-bottom: .55rem;
        margin-bottom: .55rem;
        border-bottom: 1px dashed rgba(15, 23, 42, 0.08);
        color: var(--deal-text);
        font-size: .84rem;
    }

    .deal-meta-label {
        color: var(--deal-muted);
    }

    .deal-empty-state {
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed rgba(47, 128, 237, 0.3);
        border-radius: 16px;
        color: var(--deal-muted);
        background: rgba(255, 255, 255, 0.55);
        font-size: .88rem;
    }

    @media (max-width: 991.98px) {
        .deal-board {
            grid-template-columns: repeat(6, minmax(280px, 280px));
        }
    }
</style>
@endsection
