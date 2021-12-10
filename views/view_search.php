<?php
?>
<form class="searchForm" onsubmit="return false">
    <div class="input-group">
        <span class="input-group-text btn-secondary" id="basic-addon1">
            <i style="font-size: 1rem" class="bi bi-search"></i></span>
        <input type="text" class="form-control" placeholder="Search for a user" aria-label="Search for a user"
            aria-describedby="basic-addon1" id="search" name="search_for" onclick="debounce(0)"
            oninput="debounce(500)" />
    </div>
    <div id="searchResult" class="searchResult hidden">
        <div class="paginationComm">
            <p>Page <span id="page_num"> 1</span> out of <span id="num_pages"></span> | <span id="num_result"></span>
                Results found</p>
            <div class="PaginationBtn">
                <button data-page="0" data-dir="decr" onclick="pagination(this)" type="button">&lsaquo;</button>
                <button data-page="0" data-dir="incr" onclick="pagination(this)" type="button">&rsaquo;</button>
            </div>
        </div>
        <div class="list"></div>
    </div>
</form>
<script>
function pagination(element) {
    if (element.dataset.dir === "incr") {
        const btn = document.querySelectorAll('[data-page]')
        document.querySelector("#page_num").textContent = parseInt(btn[0].dataset.page) + 2
        btn.forEach(e => {
            e.dataset.page = parseInt(e.dataset.page) + 1
        })
    }
    if (element.dataset.dir === "decr") {
        if (element.dataset.page != 0) {
            const btn = document.querySelectorAll('[data-page]')
            document.querySelector("#page_num").textContent = parseInt(btn[0].dataset.page)
            btn.forEach(e => {
                e.dataset.page = parseInt(e.dataset.page) - 1
            })
        }
    }
    const s = document.querySelector("#search")
    const value = s.value
    show_results(value)
}
let timer;

function debounce(time) {
    const s = document.querySelector("#search")
    const value = s.value
    const sibling = document.querySelector("#searchResult")
    if (event.target.value.length >= 2) {
        if (timer) {
            clearTimeout(timer)
        }
        timer = setTimeout(function() {
            sibling.classList.toggle("hidden")
            if (!sibling.classList.contains("hidden")) {
                show_results(value);
            }
        }, time)
    }
}
async function show_results(value) {
    const page = document.querySelector('[data-page]').dataset.page
    const body = {
        search_for: value,
        page: page
    }
    await $.ajax({
        type: "POST",
        url: "/search",
        data: JSON.stringify(body),
        accepts: 'application/json; charset=utf-8',
        success: function(response) {
            reveal_data(response)
        },
        error: function(result) {
            console.log(result)
        }
    });
    // const data = await conn.json()
    // reveal_data(data)
}

function reveal_data(data) {
    const res = document.querySelector('#searchResult').querySelector('.list')
    res.innerHTML = ""
    const number = data[0]['num_rows'];
    delete data[0]['num_rows'];
    document.querySelector("#num_result").textContent = number;
    document.querySelector("#num_pages").textContent = Math.ceil(parseInt(number) / 20);
    data.forEach(e => {
        const comp =
            `<div class="searchData card"><a href="/user/${e.user_id}">
            <p class="singleClick">Username: ${e.username} </p>
            <p>First Name: ${e.first_name}</p><a>
            </div>`
        res.insertAdjacentHTML("beforeend", comp)
    })
}
</script>