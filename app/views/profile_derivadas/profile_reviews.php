<div class="flex items-center mt-6">
	<a class="text-3xl font-bold text-gray-900 underline mr-2">Reseñas</a>
	<span class="text-3xl font-bold text-gray-900">(<?= count($reviews) ?>)</span>
</div>
<?php if (count($reviews) > 0) : ?>
	<div class="gap-4 mt-6 pl-2 grid grid-cols-2">
		<?php foreach ($reviews as $review) : ?>
			<div class="flex flex-col gap-1 border border-borderGrey p-2">
				<a href="book?isbn=<?= $review['isbn'] ?>" class="text-lg font-extrabold text-gray-900"><?= $review['book_title'] ?></a>
				<div class="flex items-left gap-1">
					<p class="text-sm text-black font-semibold"><?= $review['rating'] ?></p>
					<img src="img/star.svg" alt="star" class="w-4 h-4">
				</div>
				<p class="text-sm text-black font-semibold"><?= $review['comment'] ?></p>
			</div>
		<?php endforeach; ?>
	</div>
<?php else : ?>
	<p class="text-lg text-gray-900">Este usuario no ha hecho ninguna reseña.</p>
<?php endif; ?> 