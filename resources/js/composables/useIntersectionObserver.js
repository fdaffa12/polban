import { onMounted, onUnmounted } from "vue";

export function useIntersectionObserver() {
    let observer = null;

    const observeElements = () => {
        const sections = document.querySelectorAll(".float-in-section");

        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("visible");
                    }
                });
            },
            {
                threshold: 0.1,
                rootMargin: "0px 0px -50px 0px",
            }
        );

        sections.forEach((section) => {
            observer.observe(section);
        });
    };

    onMounted(() => {
        observeElements();
    });

    onUnmounted(() => {
        if (observer) {
            observer.disconnect();
        }
    });
}
