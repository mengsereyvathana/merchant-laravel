/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                body: "#F5F7FA",
                primary: "#FC8019",
                hover_menu: "#EFF2F6",
                black_500: "#141824",
                current: "#0056b3c2",
                last: "#82ca9d",
                view: "#D9FBD0",
                head: "#2b3445",
                main: "#024EA7",
                field: "#F2F4F9",
                danger: "#FA4545",
                letter: "#4E4948",
                lettermain: "#024EA7",
                background: "#F5F5F9",
            },
            fontSize: {
                title: "16px",
                subtitle: "14px",
                header: "18px",
                subheader: "16px",
                ph: "13px",
                side: "16px",
            },
            transitionDuration: {
                0: "0ms",
            },
        },
    },
    plugins: [require("tailwind-scrollbar")],
};
