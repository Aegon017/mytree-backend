@extends('Admin.layouts.admin_layout')
@section('title', 'Sponsor')

@push('styles')
    <style>
       @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap');

       @import url(https://fonts.googleapis.com/css?family=Varela+Round);


.slides {
    padding: 0;
    width: 609px;
    height: 420px;
    display: block;
    margin: 0 auto;
    position: relative;
}

.slides * {
    user-select: none;
    -ms-user-select: none;
    -moz-user-select: none;
    -khtml-user-select: none;
    -webkit-user-select: none;
    -webkit-touch-callout: none;
}

.slides input { display: none; }

.slide-container { display: block; }

.slide {
    top: 0;
    opacity: 0;
    width: 609px;
    height: 420px;
    display: block;
    position: absolute;

    transform: scale(0);

    transition: all .7s ease-in-out;
}

.slide img {
    width: 100%;
    height: 100%;
}

.nav label {
    width: 200px;
    height: 100%;
    display: none;
    position: absolute;

	  opacity: 0;
    z-index: 9;
    cursor: pointer;

    transition: opacity .2s;

    color: #FFF;
    font-size: 156pt;
    text-align: center;
    line-height: 380px;
    font-family: "Varela Round", sans-serif;
    background-color: rgba(255, 255, 255, .3);
    text-shadow: 0px 0px 15px rgb(119, 119, 119);
}

.slide:hover + .nav label { opacity: 0.5; }

.nav label:hover { opacity: 1; }

.nav .next { right: 0; }

input:checked + .slide-container  .slide {
    opacity: 1;

    transform: scale(1);

    transition: opacity 1s ease-in-out;
}

input:checked + .slide-container .nav label { display: block; }

.nav-dots {
	width: 100%;
	bottom: 9px;
	height: 11px;
	display: block;
	position: absolute;
	text-align: center;
}

.nav-dots .nav-dot {
	top: -5px;
	width: 11px;
	height: 11px;
	margin: 0 4px;
	position: relative;
	border-radius: 100%;
	display: inline-block;
	background-color: rgba(0, 0, 0, 0.6);
}

.nav-dots .nav-dot:hover {
	cursor: pointer;
	background-color: rgba(0, 0, 0, 0.8);
}

input#img-1:checked ~ .nav-dots label#img-dot-1,
input#img-2:checked ~ .nav-dots label#img-dot-2,
input#img-3:checked ~ .nav-dots label#img-dot-3,
input#img-4:checked ~ .nav-dots label#img-dot-4,
input#img-5:checked ~ .nav-dots label#img-dot-5,
input#img-6:checked ~ .nav-dots label#img-dot-6 {
	background: rgba(0, 0, 0, 0.8);
}


/* 
*{
    box-sizing: border-box;
    padding: 0;
    margin: 0;
    font-family: 'Open Sans', sans-serif;
}
body{
    line-height: 1.5;
} */
/* .card-wrapper{
    max-width: 900px;
    margin: 0 auto;
} */
img{
    width: 100%;
    display: block;
}
.img-display{
    overflow: hidden;
}
.img-showcase{
    display: flex;
    width: 100%;
    transition: all 0.5s ease;
}
.img-showcase img{
    min-width: 100%;
}
.img-select{
    display: flex;
}
.img-item{
    margin: 0.3rem;
}
.img-item:nth-child(1),
.img-item:nth-child(2),
.img-item:nth-child(3){
    margin-right: 0;
}
.img-item:hover{
    opacity: 0.8;
}
.product-content{
    padding: 2rem 1rem;
}
.product-title{
    font-size: 2rem;
    text-transform: capitalize;
    font-weight: 700;
    position: relative;
    color: #12263a;
    margin: 1rem 0;
}
.product-title::after{
    content: "";
    position: absolute;
    left: 0;
    bottom: 0;
    height: 4px;
    width: 80px;
    background: #12263a;
}
.product-link{
    text-decoration: none;
    text-transform: uppercase;
    font-weight: 400;
    font-size: 0.9rem;
    display: inline-block;
    margin-bottom: 0.5rem;
    background: #256eff;
    color: #fff;
    padding: 0 0.3rem;
    transition: all 0.5s ease;
}
.product-link:hover{
    opacity: 0.9;
}
.product-rating{
    color: #ffc107;
}
.product-rating span{
    font-weight: 600;
    color: #252525;
}
.product-price{
    margin: 1rem 0;
    font-size: 1rem;
    font-weight: 700;
}
.product-price span{
    font-weight: 400;
}
.last-price span{
    color: #f64749;
    text-decoration: line-through;
}
.new-price span{
    color: #256eff;
}
.product-detail h2{
    text-transform: capitalize;
    color: #12263a;
    padding-bottom: 0.6rem;
}
.product-detail p{
    font-size: 0.9rem;
    padding: 0.3rem;
    opacity: 0.8;
}
.product-detail ul{
    margin: 1rem 0;
    font-size: 0.9rem;
}
.product-detail ul li{
    margin: 0;
    list-style: none;
    background: url(https://fadzrinmadu.github.io/hosted-assets/product-detail-page-design-with-image-slider-html-css-and-javascript/checked.png) left center no-repeat;
    background-size: 18px;
    padding-left: 1.7rem;
    margin: 0.4rem 0;
    font-weight: 600;
    opacity: 0.9;
}
.product-detail ul li span{
    font-weight: 400;
}
.purchase-info{
    margin: 1.5rem 0;
}
.purchase-info input,
.purchase-info .btn{
    border: 1.5px solid #ddd;
    border-radius: 25px;
    text-align: center;
    padding: 0.45rem 0.8rem;
    outline: 0;
    margin-right: 0.2rem;
    margin-bottom: 1rem;
}
.purchase-info input{
    width: 60px;
}
.purchase-info .btn{
    cursor: pointer;
    color: #fff;
}
.purchase-info .btn:first-of-type{
    background: #256eff;
}
.purchase-info .btn:last-of-type{
    background: #f64749;
}
.purchase-info .btn:hover{
    opacity: 0.9;
}
.social-links{
    display: flex;
    align-items: center;
}
.social-links a{
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    color: #000;
    border: 1px solid #000;
    margin: 0 0.2rem;
    border-radius: 50%;
    text-decoration: none;
    font-size: 0.8rem;
    transition: all 0.5s ease;
}
.social-links a:hover{
    background: #000;
    border-color: transparent;
    color: #fff;
}

@media screen and (min-width: 992px){
    .card{
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-gap: 1.5rem;
    }
    .card-wrapper{
        /* height: 100vh; */
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .product-imgs{
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .product-content{
        padding-top: 0;
    }
}
    </style>
@endpush

@section('content')
    <div class="container">
        <div id="flash-message">
        </div>
        <br/>
        
        <div class = "card-wrapper">
            <div class = "card">
                <!-- card left -->
                <div class = "product-imgs">
                <div>
                    <ul class="slides">
                        <!-- Main image, initially checked -->
                        <input type="radio" name="radio-btn" id="img-1" checked />
                        <li class="slide-container">
                            <div class="slide">
                                <img src="{{ $tree->main_image_url }}" />
                            </div>
                            <div class="nav">
                                <label for="img-{{ count($tree->images) + 1 }}" class="prev">&#x2039;</label>
                                <label for="img-2" class="next">&#x203a;</label>
                            </div>
                        </li>

                        <!-- Additional images -->
                        @if(count($tree->images) > 0)
                        <?php $k = 2; ?>
                        @foreach($tree->images as $image)
                            <input type="radio" name="radio-btn" id="img-{{$k}}" />
                            <li class="slide-container">
                                <div class="slide">
                                    <img src="{{ $image->image_url }}" />
                                </div>
                                <div class="nav">
                                    <label for="img-{{ $k - 1 }}" class="prev">&#x2039;</label>
                                    <label for="img-{{ $k + 1 }}" class="next">&#x203a;</label>
                                </div>
                            </li>
                            <?php $k++; ?>
                        @endforeach
                        @endif

                        <!-- Navigation Dots -->
                        <li class="nav-dots">
                            @for ($i = 1; $i <= $k; $i++)
                                <label for="img-{{$i}}" class="nav-dot" id="img-dot-{{$i}}"></label>
                            @endfor
                        </li>
                    </ul>
                </div>

                </div>
                <!-- card right -->
                <div class = "product-content">
                <h2 class = "product-title">{{$tree->name}}</h2>
                <a href = "#" class = "product-link">{{$tree->age}} Years</a>
                <div class = "product-rating">
                    <span>Quantity : {{$tree->quantity}}</span>
                </div>


                <div class = "product-detail">
                    
                    
                    <ul>
                    <li>State: <span>{{$tree->state->name ?? 'NA'}}</span></li>
                    <li>City: <span>{{$tree->city->name ?? 'NA'}}</span></li>
                    <li>Area: <span>{{$tree->area->name ?? 'NA'}}</span></li>
                    </ul>
                </div>

                <div class = "purchase-info">
                    <!-- <input type = "number" min = "0" value = "1"> -->
                    <button type = "button" class = "btn">
                    {{$tree->sku}}
                    </button>
                    <button type = "button" class = "btn">{{$tree->age}} Years</button>
                </div>
                </div>

                <div class="row">
                    <div class="col-md-12" style="padding: 31px;">
                        <!-- Tab -->
                        <nav>
                            <div class="nav nav-tabs mb-4" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-description-tab" data-bs-toggle="tab" href="#nav-description" role="tab" aria-controls="nav-description" aria-selected="true">Description</a>
                                <a class="nav-item nav-link" id="nav-price-info-tab" data-bs-toggle="tab" href="#nav-price-info" role="tab" aria-controls="nav-price-info" aria-selected="false">Price Info</a>
                                <a class="nav-item nav-link" id="nav-prices-tab" data-bs-toggle="tab" href="#nav-prices" role="tab" aria-controls="nav-prices" aria-selected="false">Prices</a>
                                @if($tree->adopted_status ==1)
                                <a class="nav-item nav-link" id="nav-relations-tab" data-bs-toggle="tab" href="#nav-relations" role="tab" aria-controls="nav-relations" aria-selected="false">Adoption Details</a>
                                @endif
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-description" role="tabpanel" aria-labelledby="nav-description-tab">
                            {!! $tree->description !!}
                        </div>
                            <div class="tab-pane fade" id="nav-price-info" role="tabpanel" aria-labelledby="nav-price-info-tab">
                            {!! $tree->price_info !!}
                        </div>
                            <div class="tab-pane fade" id="nav-prices" role="tabpanel" aria-labelledby="nav-prices-tab">
                                <div class="product-prices">
                                    <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Duration</th>
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tree->price as $price)
                                                <tr>
                                                    <td>{{$price->duration}} Years</td>
                                                    <td>{{$price->price}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                            @if($tree->adopted_status ==1)
                            <div class="tab-pane fade show" id="nav-relations" role="tabpanel" aria-labelledby="nav-relations-tab">
                                <div class="table-responsive">    
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>User</th>
                                            <th>Order</th>
                                            <th>Subscription Start</th>
                                            <th>Subscription End</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tree->userRelations as $relation)
                                        <tr>
                                            <td>{{ $relation->user->name ?? '' }}</td>
                                            <td>{{ $relation->order->order_ref }}</td>
                                            <td>{{ $relation->subscription_start }}</td>
                                            <td>{{ $relation->subscription_end }}</td>
                                            <td>{{ $relation->status }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>                             
                                </div>
                            </div>
                            @endif
                        </div>
                        <!-- End of tab -->
                    </div>
                </div>
            </div>

            
        </div>

</div>
@endsection
@section('script')
    @parent
    <script>

    const imgs = document.querySelectorAll('.img-select a');
    const imgBtns = [...imgs];
    let imgId = 1;

    imgBtns.forEach((imgItem) => {
        imgItem.addEventListener('click', (event) => {
            event.preventDefault();
            imgId = imgItem.dataset.id;
            slideImage();
        });
    });

    function slideImage(){
        const displayWidth = document.querySelector('.img-showcase img:first-child').clientWidth;

        document.querySelector('.img-showcase').style.transform = `translateX(${- (imgId - 1) * displayWidth}px)`;
    }
    window.addEventListener('resize', slideImage);
    </script>
@endsection
