export const rippleInit = {
    mounted(el: HTMLElement) {
        function rippleEffect(event: MouseEvent) {
            const time = 20000;
            const self = el;
            const rect = self.getBoundingClientRect();
            const x = event.clientX - rect.left;
            const y = event.clientY - rect.top;
            const circle = document.createElement("span");
            circle.style.top = y + "px";
            circle.style.left = x + "px";
            circle.className = "ripple animate";
            self.appendChild(circle);

            setTimeout(() => {
                self.removeChild(circle);
            }, time);
        }
        el.addEventListener("mousedown", rippleEffect as EventListener, true);
    },
};
