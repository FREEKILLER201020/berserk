<body>
<script>
document.addEventListener('keydown', function(event) {
  console.log(event.shiftKey);
  if (event.code == 'KeyZ' && (event.ctrlKey || event.metaKey)) {
    alert('Undo!')
  }
});
</script>
</body>
