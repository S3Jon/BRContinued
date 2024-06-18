<div class="grid grid-cols-4 gap-12 justify-center w-3/4 content-center pt-4 pl-10">
    <?php if (count($foundusers) > 0) : ?>
        <?php foreach ($foundusers as $user) : ?>
            <div class="flex gap-1 bg-[rgba(36,38,51,0.15)] shadow-md rounded-lg p-2">
                <div class="flex items-center">
                <img src="<?= !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'img/maestro.svg' ?>" alt="<?= htmlspecialchars($user['username']) ?>" class="w-20 h-20 object-image rounded-lg">
                </div>
                <div class="flex flex-col">
                    <a href="profile?id=<?= $user['id_user'] ?>" class="text-lg font-extrabold text-gray-900"><?= $user['username'] ?></a>
                    <div class="flex items-center gap-2 my-1 ml-1">
                        <img src="img/followers.svg" alt="followers" class="w-4 h-4">
                        <p class="text-sm text-black font-semibold"><?= $user['followersNum'] ?></p>
                    </div>
                    <div class="flex items-center gap-2 my-1 ml-1">
                        <img src="img/lists-o.svg" alt="lists" class="w-4 h-4">
                        <p class="text-sm text-black font-semibold"><?= $user['publicListsCount'] ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p class="text-lg text-gray-900">No se encontraron usuarios que coincidan con esos términos.</p>
    <?php endif; ?>
</div>
