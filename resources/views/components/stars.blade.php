@props(['value' => 0])
<span {{ $attributes->class('vs-stars') }} style="--p: {{ max(0, min(5, (float) $value)) / 5 * 100 }}%"
      role="img" aria-label="Rating {{ number_format((float) $value, 1, ',', '.') }} dari 5">★★★★★</span>
