// Subtle reveal on scroll
(function(){
  const els = document.querySelectorAll('[data-reveal]');
  if (!('IntersectionObserver' in window) || !els.length) return;
  const io = new IntersectionObserver((entries)=>{
    entries.forEach(en=>{
      if (en.isIntersecting){
        en.target.style.opacity = 1;
        en.target.style.transform = 'translateY(0)';
        io.unobserve(en.target);
      }
    });
  },{threshold:.12});
  els.forEach(el=>{
    el.style.opacity = 0;
    el.style.transform = 'translateY(18px)';
    el.style.transition = 'opacity .6s ease, transform .6s ease';
    io.observe(el);
  });
})();

// Property detail gallery swap
(function(){
  const main = document.querySelector('.gallery .main');
  if (!main) return;
  document.querySelectorAll('.gallery .thumb').forEach(t=>{
    t.addEventListener('click', ()=>{
      const url = t.style.backgroundImage;
      const prev = main.style.backgroundImage;
      main.style.backgroundImage = url;
      t.style.backgroundImage = prev;
    });
  });
})();

// Close nav on link click (mobile)
document.querySelectorAll('.main-nav a').forEach(a=>{
  a.addEventListener('click', ()=> document.body.classList.remove('nav-open'));
});
