import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/*
 * AJAX: tombol "Mulai" / "Selesai" di kartu tugas.
 * Mengirim PATCH ke /tasks/{id}/status tanpa memuat ulang halaman,
 * lalu memperbarui badge status dan tombolnya dari jawaban JSON server.
 */
document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-next-status]');
    if (!button) return;

    const card = button.closest('[data-task-card]');
    button.disabled = true;

    try {
        const response = await fetch(button.dataset.nextStatus, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });

        if (!response.ok) throw new Error('Gagal mengubah status');

        const data = await response.json();

        const badge = card.querySelector('[data-status-badge]');
        badge.textContent = data.label;
        badge.className = 'inline-flex shrink-0 items-center rounded-full px-2.5 py-0.5 text-xs font-medium ' + data.color;

        if (data.done) {
            card.querySelector('[data-task-title]').classList.add('line-through', 'text-gray-400');
            button.remove();
        } else {
            button.textContent = 'Selesai';
            button.disabled = false;
        }
    } catch (error) {
        alert('Status gagal diubah. Coba muat ulang halaman.');
        button.disabled = false;
    }
});
