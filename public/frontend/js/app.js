const initCarousel = () => {
    const carousel = document.getElementById("carousel");

    if (!carousel || carousel.dataset.carouselReady === "true") {
        return;
    }

    carousel.dataset.carouselReady = "true";

    let isDragging = false;
    let startX = 0;
    let startScrollLeft = 0;
    let moved = false;
    let activePointerId = null;

    const endDrag = () => {
        if (!isDragging) {
            return;
        }

        isDragging = false;

        if (activePointerId !== null) {
            carousel.releasePointerCapture?.(activePointerId);
            activePointerId = null;
        }

        carousel.classList.remove("cursor-grabbing");

        window.setTimeout(() => {
            moved = false;
        }, 0);
    };

    carousel.addEventListener("dragstart", (event) => {
        event.preventDefault();
    });

    carousel.addEventListener(
        "click",
        (event) => {
            if (!moved) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
        },
        true,
    );

    carousel.addEventListener("pointerdown", (event) => {
        if (event.pointerType !== "mouse" || event.button !== 0) {
            return;
        }

        isDragging = true;
        moved = false;
        startX = event.clientX;
        startScrollLeft = carousel.scrollLeft;
        activePointerId = event.pointerId;

        carousel.setPointerCapture?.(event.pointerId);
        carousel.classList.add("cursor-grabbing");
    });

    carousel.addEventListener("pointermove", (event) => {
        if (!isDragging) {
            return;
        }

        const delta = event.clientX - startX;

        if (Math.abs(delta) > 5) {
            moved = true;
        }

        carousel.scrollLeft = startScrollLeft - delta;
    });

    carousel.addEventListener("pointerup", endDrag);
    carousel.addEventListener("pointercancel", endDrag);
};

const bootCarousel = () => {
    initCarousel();
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bootCarousel);
} else {
    bootCarousel();
}

document.addEventListener("livewire:navigated", bootCarousel);
