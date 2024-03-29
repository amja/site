const BUTTONS_CLASS_NAME = "buttons";
const WINDOWS_CLASS = "window";
const WINDOW_ENLARGED_CLASS = "enlarged";
const WINDOW_ABSOLUTE_CLASS = "absolute";
const WINDOW_PLACEHOLDER_CLASS = "window-placeholder";
const LOADING_VIEW_CLASS = "loading-view";
const TEXT_CONTAINER_CLASS = "text-container";
const MAIN_WRAPPER = "main-wrapper";
const PUSH_BACK_CLASS = "pushed-back";
const POSSUMS_SHOW_CLASS = "possums-shown";
const MAX_ZOOM_WIDTH = 3500000;
const POSSIBLE_TEXTS = [
    `Is it time to log off?`,
    `We all go too far sometimes...`,
    `[Something philosophical about pixels]`,
    `Enjoy a quiet moment`,
    `Is it a pinwheel or a beach ball?`,
    `Are the two faces on the Finder icon friends or lovers?`,
    `Flipping some transistors just to feel a little warmth...`,
    `We take it for granted today, but a single computer screen has more pixels than a peasant in the 1400s would get in their whole lifetime <a href="https://twitter.com/matthewpcrowley/status/621078253827002368">*</a>`
];
var usedTexts = [];
var windowElement;

window.onload = () => {
    windowElement = document.getElementsByClassName(WINDOWS_CLASS)[0];
    updateAge();

    setupWindowDraggingBehaviour();
};

const updateAge = () => {
    const ageParagraph = document.getElementById("replace-age");
    const age = Math.floor((new Date() - new Date("1997-09-30")) / (365.25 * 24 * 60 * 60 * 1000));
    ageParagraph.innerHTML = ageParagraph.innerHTML.replace(/\d{2,3} years old/, `${age} years old`);
};

const onCloseButtonTapped = () => {
    makeWindowAbsolute();
    windowElement.style.display = "none";
    document.getElementById("face-1").focus();
};

const onMinimiseButtonTapped = () => {
    const todo = document.getElementsByClassName("todo")[0];
    const originalContent = todo.innerHTML;
    todo.innerHTML = "";
    todo.innerHTML = originalContent;

    todo.classList.add("flash");
    todo.style.animation = "none";
    todo.offsetHeight;
    todo.style.animation = null;
}

const onFullScreenButtonTapped = () => {
    const announcementElement = document.getElementsByClassName("announcement")[0];
    announcementElement.style.display = "initial";
    // Various extra work required for consistent screen reader announcement
    announcementElement.innerHTML = "<span>Imitation window expands until photo pixels are visible and then fades to black.</span>";
    setTimeout(() => {
        announcementElement.style.display = "none";
        announcementElement.innerHTML = "";
    }, 500);

    if (usedTexts.length == POSSIBLE_TEXTS.length) {
        usedTexts = []
    }
    const unusedTexts = POSSIBLE_TEXTS.filter(item => { return !usedTexts.includes(item) });
    const nextText = unusedTexts[Math.floor(Math.random() * unusedTexts.length)];
    usedTexts.push(nextText);
    document.getElementsByClassName(TEXT_CONTAINER_CLASS)[0].innerHTML = nextText;
    makeWindowAbsolute();

    const originalSize = windowElement.getBoundingClientRect();
    const addedHeight = MAX_ZOOM_WIDTH * originalSize.height / originalSize.width;

    windowElement.classList.add(WINDOW_ENLARGED_CLASS);
    windowElement.style.width = px(originalSize.width + MAX_ZOOM_WIDTH);
    windowElement.style.left = px(originalSize.left - MAX_ZOOM_WIDTH / 2);
    windowElement.style.top = px(originalSize.top - addedHeight / 2);

    document.getElementsByClassName(LOADING_VIEW_CLASS)[0].classList.add("display");
    document.getElementById(MAIN_WRAPPER).ariaHidden = true;
}

const makeWindowAbsolute = () => {
    if (windowElement.classList.contains(WINDOW_ABSOLUTE_CLASS)) {
        return;
    }

    const windowPosition = windowElement.getBoundingClientRect()
    windowElement.classList.add(WINDOW_ABSOLUTE_CLASS);
    windowElement.style.top = px(windowPosition.top);
    windowElement.style.left = px(windowPosition.left);
    windowElement.style.width = px(windowPosition.width);

    const placeholderElement = document.getElementsByClassName(WINDOW_PLACEHOLDER_CLASS)[0];
    placeholderElement.style.width = px(windowPosition.width);
    placeholderElement.style.height = px(windowPosition.height);
    placeholderElement.style.display = "flex";
};

const setupWindowDraggingBehaviour = () => {
    document.getElementsByClassName("chrome")[0].onmousedown = onChromeDragStarted;
};

const onChromeDragStarted = e => {
    if (e.target.parentElement.className == BUTTONS_CLASS_NAME) {
        return false;
    }

    e.preventDefault();
    e.target.style.cursor = "grabbing";

    var mouseX = e.clientX;
    var mouseY = e.clientY;
    makeWindowAbsolute();

    document.onmouseup = () => {
        e.target.style.cursor = "grab";
        document.onmouseup = null;
        document.onmousemove = null;
    }

    document.onmousemove = e => {
        const dX = e.clientX - mouseX
        const dY = e.clientY - mouseY
        mouseX = e.clientX
        mouseY = e.clientY

        const originalRect = windowElement.getBoundingClientRect();
        windowElement.style.left = px(originalRect.left + dX);
        windowElement.style.top = px(originalRect.top + dY);
    }
};

const goBack = () => {
    windowElement.classList.remove(WINDOW_ENLARGED_CLASS);
    windowElement.classList.remove(WINDOW_ABSOLUTE_CLASS);
    windowElement.style.width = null;
    windowElement.style.left = null;
    windowElement.style.top = null;
    document.getElementsByClassName(WINDOW_PLACEHOLDER_CLASS)[0].style.display = "none";

    document.getElementsByClassName(LOADING_VIEW_CLASS)[0].classList.remove("display");
    document.getElementById(MAIN_WRAPPER).ariaHidden = false;
};

const px = val => `${val}px`;

const animatePossums = () => {
    const items = document.querySelectorAll (".possum-wrapper img");
    var tick = 0;
    const updateFrame = () => {
        // To update when window resizes.
        const circleRadius = Math.min(window.innerWidth, window.innerHeight) / 2;
        const wrapperElement = document.getElementsByClassName("possum-wrapper")[0];
        for (i = 0; i < items.length; i++) {
            const angle = (i / items.length) * (2 * Math.PI) + tick;
            items[i].style.top = px(Math.sin(angle) * circleRadius / 2 + wrapperElement.offsetTop + wrapperElement.offsetHeight / 2 - items[i].offsetHeight / 2);
            items[i].style.left = px(Math.cos(angle) * circleRadius + wrapperElement.offsetLeft + wrapperElement.offsetWidth / 2 - items[i].offsetWidth / 2);
            items[i].style.transform = `perspective(100px) translateZ(${Math.sin(angle) * 25}px)`
        }
        tick += 0.002
    };

    if (!window.matchMedia("(prefers-reduced-motion)").matches) {
        setInterval(updateFrame, 10);
    } else {
        updateFrame();
    }
    
    for (i = 0; i < 20; i++) {
        const div = document.createElement("div");
        div.innerHTML = "💖";
        div.ariaHidden = true;
        div.style.rotate = `${Math.floor((Math.random() - 0.5) * 45)}deg`;
        div.className = "heart";
        document.getElementsByClassName("possums")[0].appendChild(div);
    }

    for (heart of document.getElementsByClassName("heart")) {
        heart.style.top = px(Math.random() * window.innerHeight);
        heart.style.left = px(Math.random() * window.innerWidth);
    }
}

const showPossums = () => {
    animatePossums();
    document.getElementById(MAIN_WRAPPER).classList.add(PUSH_BACK_CLASS);
    document.getElementsByClassName("possums")[0].classList.add(POSSUMS_SHOW_CLASS);
    document.getElementById(MAIN_WRAPPER).ariaHidden = true;
}

const closePossums = () => {
    document.getElementsByClassName("possums")[0].classList.remove(POSSUMS_SHOW_CLASS);
    document.getElementById(MAIN_WRAPPER).classList.remove(PUSH_BACK_CLASS);
    document.getElementById(MAIN_WRAPPER).ariaHidden = false;
}