document.addEventListener('DOMContentLoaded',()=>{
  const search=document.getElementById('tableSearch');
  if(search) search.addEventListener('input',()=>{const q=search.value.toLowerCase();document.querySelectorAll('#dataTable tbody tr').forEach(r=>r.style.display=r.innerText.toLowerCase().includes(q)?'':'none');});
  const canvas=document.getElementById('salesChart');
  if(canvas && window.Chart){new Chart(canvas,{type:'line',data:{labels:window.salesLabels||[],datasets:[{label:'Penjualan',data:window.salesValues||[],borderColor:'#168779',backgroundColor:'rgba(22,135,121,.12)',fill:true,tension:.35,borderWidth:3,pointRadius:4}]},options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{callback:v=>'Rp'+Number(v).toLocaleString('id-ID')}},x:{grid:{display:false}}}}});}
  const grid=document.getElementById('productGrid'), cartEl=document.getElementById('cartItems');
  if(grid && cartEl){
    const cart={}; const totalEl=document.getElementById('cartTotal');
    const money=n=>'Rp'+Number(n).toLocaleString('id-ID');
    function render(){
      cartEl.innerHTML='';
      const items=Object.values(cart);
      if(!items.length) cartEl.innerHTML='<p class="empty">Pilih menu untuk memulai pesanan.</p>';
      items.forEach(item=>{
        const row=document.createElement('div');row.className='cart-row';
        row.innerHTML=`<div><b>${item.name}</b><small>${money(item.price)} × ${item.qty}</small></div><div class="qty-controls"><button type="button" data-dec="${item.id}">−</button><span>${item.qty}</span><button type="button" data-inc="${item.id}">+</button></div>`;
        cartEl.appendChild(row);
      });
      const total=items.reduce((s,i)=>s+i.price*i.qty,0);totalEl.textContent=money(total);
      document.getElementById('cartJson').value=JSON.stringify(items.map(i=>({id:i.id,qty:i.qty})));
    }
    grid.querySelectorAll('.product-tile').forEach(btn=>btn.addEventListener('click',()=>{
      const id=btn.dataset.id,stock=Number(btn.dataset.stock);
      if(cart[id] && cart[id].qty>=stock){alert('Jumlah melebihi stok tersedia.');return;}
      if(!cart[id])cart[id]={id:Number(id),name:btn.dataset.name,price:Number(btn.dataset.price),qty:0,stock};
      cart[id].qty++;render();
    }));
    cartEl.addEventListener('click',ev=>{
      const dec=ev.target.dataset.dec,inc=ev.target.dataset.inc;
      if(dec && cart[dec]){cart[dec].qty--;if(cart[dec].qty<=0)delete cart[dec];}
      if(inc && cart[inc]){if(cart[inc].qty>=cart[inc].stock){alert('Stok tidak cukup.');return;}cart[inc].qty++;}
      render();
    });
    const ps=document.getElementById('productSearch');
    if(ps)ps.addEventListener('input',()=>{const q=ps.value.toLowerCase();grid.querySelectorAll('.product-tile').forEach(b=>b.style.display=b.dataset.name.toLowerCase().includes(q)?'':'none');});
    document.getElementById('checkoutForm').addEventListener('submit',ev=>{
      if(!Object.keys(cart).length){ev.preventDefault();alert('Keranjang masih kosong.');return;}
      document.getElementById('customerField').value=document.getElementById('customerName').value||'Umum';
    });
  }
});
