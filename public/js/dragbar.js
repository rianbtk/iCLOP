let left = document.getElementById("drag-left"),
right = document.getElementById("drag-right"),
bar = document.getElementById("dragbar");

const drag = e => {
document.selection
  ? document.selection.empty()
  : window.getSelection().removeAllRanges();
left.style.width = e.pageX - bar.offsetWidth + "px";
};

bar.addEventListener("mousedown", () => {
document.addEventListener("mousemove", drag);
});

bar.addEventListener("mouseup", () => {
document.removeEventListener("mousemove", drag);
});