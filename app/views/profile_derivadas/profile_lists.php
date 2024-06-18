<div class="grid grid-cols-3 mt-6">
	<div class="flex items-center">
		<a class="text-3xl font-bold text-gray-900 underline mr-2">Listas</a>
		<span class="text-3xl font-bold text-gray-900">(<?= count($createdLists) ?>)</span>
	</div>
</div>
<?php if (count($createdLists) > 0) : ?>
	<div class="gap-4 mt-4 pl-2 grid grid-cols-2">
		<?php foreach ($createdLists as $list) : ?>
			<div class="flex gap-1 bg-[rgba(36,38,51,0.15)] shadow-md rounded-lg p-2">
				<div class="flex items-center">
					<?php if (!empty($list['book_image'])) : ?>
						<img src="<?= $list['book_image'] ?>" alt="book cover" class="w-20 h-auto rounded-lg">
					<?php else : ?>
						<div class="w-20 h-auto rounded-lg bg-gray-200 flex items-center justify-center">No hay imagen disponible</div>
					<?php endif; ?>
				</div>
				<div class="flex flex-col">
					<a href="list?id=<?= $list['id_list'] ?>" class="text-lg font-extrabold text-gray-900"><?= $list['list_name'] ?></a>
					<div class="flex items-center gap-2 my-2 ml-1">
						<img src="img/followers.svg" alt="followers" class="w-4 h-4">
						<p class="text-sm text-black font-semibold"><?= $list['followersNum'] ?></p>
					</div>
					<div class="flex items-center gap-2 my-2 ml-1">
						<img src="img/bookStack.svg" alt="bils" class="w-4 h-4">
						<p class="text-sm text-black font-semibold"><?= $list['BILCount'] ?></p>
					</div>
					<div class="flex items-center gap-2 my-2 ml-1">
						<p class="text-sm text-black font-semibold"><?= $list['list_description'] ?></p>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php else : ?>
	<p class="text-lg text-gray-900">Este usuario no tiene listas públicas.</p>
<?php endif; ?>
