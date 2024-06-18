<div class="flex items-center mt-6">
	<a class="text-3xl font-bold text-gray-900 underline mr-2">Listas seguidas</a>
	<span class="text-3xl font-bold text-gray-900">(<?= count($followedLists) ?>)</span>
</div>
<?php if (count($followedLists) > 0) : ?>
	<div class="grid grid-cols-2 gap-4 mt-6 pl-2">
		<?php foreach ($followedLists as $list) : ?>
			<div class="flex gap-4 bg-[rgba(36,38,51,0.15)] shadow-md rounded-lg p-2">
			<?php if (!empty($list['list_pic'])) : ?>
				<img src="<?= $list['list_pic'] ?>" alt="book cover" class="w-20 h-auto rounded-lg">
			<?php else : ?>
				<!-- Aquí puedes agregar un marcador de posición o mensaje si no hay imagen -->
				<div class="w-20 h-auto rounded-lg bg-gray-200 flex items-center justify-center">No hay imagen disponible</div>
			<?php endif; ?>
			<div class="flex flex-col">
				<a href="list?id=<?= $list['id_list'] ?>" class="text-lg font-extrabold text-gray-900"><?= $list['list_name'] ?></a>
				<div class="flex items-center gap-2 my-1 ml-1">
					<img src="img/users.svg" alt="user" class="w-4 h-4">
					<a href="profile?id=<?= $list['id_user'] ?>" class="text-sm text-black font-semibold"><?= $list['ownerName'] ?></a>
				</div>
				<div class="flex items-center gap-2 my-1 ml-1">
					<img src="img/followers.svg" alt="followers" class="w-4 h-4">
					<p class="text-sm text-black font-semibold"><?= $list['followersNum'] ?></p>
				</div>
				<div class="flex items-center gap-2 my-1 ml-1">
					<img src="img/bookStack.svg" alt="bils" class="w-4 h-4">
					<p class="text-sm text-black font-semibold"><?= $list['BILCount'] ?></p>
				</div>
				<p class="text-sm text-black font-semibold"><?= $list['list_description'] ?></p>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
<?php else : ?>
	<p class="text-lg text-gray-900">Este usuario no sigue ninguna lista.</p>
<?php endif; ?>