<section style="display: grid; gap: 20px; margin-bottom: 20px;">
    @foreach ($this->promotions as $promotion)
        <article wire:key="{{ $promotion->id }}" style="align-items: flex-start; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
            <h3 style="margin-top: 0; color: var(--accent);">{{ $promotion->title }}</h3>
            <p style="margin-bottom: 0;">{{ $promotion->text }}</p>
        </article>
    @endforeach
</section>
