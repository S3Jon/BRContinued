<div class="grid grid-cols-3 mt-6">
    <div class="flex items-center">
        <a class="text-3xl font-bold text-gray-900 underline mr-2">Seguidores</a>
        <span class="text-3xl font-bold text-gray-900">(<?= count($followers) ?>)</span>
    </div>
</div>
<?php if (count($followers) > 0) : ?>
    <div class="gap-4 mt-4 pl-2 grid grid-cols-2">
        <?php foreach ($followers as $follower) : ?>
			<div class="container">
				<div class="bg-[rgba(36,38,51,0.15)] shadow-md rounded-lg w-full p-3">
					<div class="flex fex-row gap-1">
						<img src="<?= !empty($follower['profile_image']) ? htmlspecialchars($follower['profile_image']) : 'img/maestro.svg' ?>" alt="<?= htmlspecialchars($userInfo['username']) ?>" class="w-20 h-20 object-image rounded-lg">
						<div class="flex flex-col">
							<a href="profile?id=<?= $follower['id_user'] ?>" class="text-lg font-extrabold text-gray-900"><?= $follower['username'] ?></a>
						</div>
					</div>
				</div>
			</div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <p class="text-lg text-gray-900">Este usuario no tiene seguidores.</p>
<?php endif; ?>
