<script>
    let slideIndex = 0;
    showSlides();

    function showSlides() {
        let slides = document.getElementsByClassName("slides");
        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {slideIndex = 1}
        slides[slideIndex-1].style.display = "block";
        setTimeout(showSlides, 5000);
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
    }

    function loadMore() {
        const container = document.getElementById('petition-container');
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'load_more_petitions.php', true);
        xhr.onload = function() {
            if (this.status === 200) {
                const petitions = JSON.parse(this.responseText);
                petitions.forEach(petition => {
                    const div = document.createElement('div');
                    div.classList.add('bg-white', 'shadow', 'rounded-lg', 'overflow-hidden', 'petition-card');
                    div.innerHTML = `
                        <img src="https://placehold.co/300x200?text=" alt="Petition image" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-bold text-lg">${petition.title}</h3>
                            <p class="text-sm mt-2 text-gray-700">${petition.content}</p>
                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-gray-600 text-sm">청원기간: ${petition.created_at}</span>
                                <button class="text-blue-600 hover:underline">자세히 보기</button>
                            </div>
                            <div class="mt-4 flex justify-between items-center">
                                <form method="post">
                                    <input type="hidden" name="like_petition" value="1">
                                    <input type="hidden" name="petition_id" value="${petition.id}">
                                    <button type="submit" class="text-gray-600 hover:underline"><i class="far fa-heart"></i> 좋아요</button>
                                </form>
                                <span class="text-gray-600 text-sm">${petition.likes} Likes</span>
                            </div>
                        </div>
                    `;
                    container.appendChild(div);
                });
            }
        };
        xhr.send();
    }

    function checkLogin(modalId) {
        <?php if (!isset($_SESSION['userid'])) { ?>
            document.getElementById('messageText').innerText = "로그인 후 이용 가능합니다.";
            openModal('messageModal');
        <?php } else { ?>
            openModal(modalId);
        <?php } ?>
    }

 
    function likePetition(petitionId) {
<<<<<<< HEAD
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "like_petitions.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            document.getElementById('messageText').innerText = response.message;
            openModal('messageModal');
            if (response.like_count !== undefined) {
                document.getElementById(`like-count-${petitionId}`).innerText = response.like_count + " Likes";
            }
            if (response.status === "liked") {
                var likeIcon = document.getElementById(`like-icon-${petitionId}`);
                likeIcon.classList.remove('far');
                likeIcon.classList.add('fas', 'text-red-500');
            }
        }
    };
    xhr.send("like_petition=1&petition_id=" + petitionId);
}


=======
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "like_petitions.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                document.getElementById('messageText').innerText = response.message;
                openModal('messageModal');
                if (response.like_count !== undefined) {
                    document.getElementById(`like-count-${petitionId}`).innerText = response.like_count + " Likes";
                }
            }
        };
        xhr.send("like_petition=1&petition_id=" + petitionId);
    }
>>>>>>> 8f8f86ab80aa31e1233fffea55dc3c523034dc39


    window.onload = function() {
        <?php if ($message) { ?>
            document.getElementById('messageText').innerText = "<?php echo $message; ?>";
            openModal('messageModal');
        <?php } ?>
    }
</script>
