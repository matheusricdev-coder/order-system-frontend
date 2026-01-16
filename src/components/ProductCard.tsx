import { Heart } from "lucide-react";

interface ProductCardProps {
  id: number;
  title: string;
  price: number;
  originalPrice?: number;
  image: string;
  freeShipping?: boolean;
}

const ProductCard = ({ title, price, originalPrice, image, freeShipping }: ProductCardProps) => {
  const discount = originalPrice 
    ? Math.round(((originalPrice - price) / originalPrice) * 100) 
    : 0;

  const formatPrice = (value: number) => {
    return value.toLocaleString('pt-BR', {
      style: 'currency',
      currency: 'BRL',
    });
  };

  return (
    <div className="product-card cursor-pointer group">
      <div className="relative aspect-square bg-card overflow-hidden">
        <img
          src={image}
          alt={title}
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        />
        <button 
          className="absolute top-2 right-2 p-1.5 bg-card/80 backdrop-blur-sm rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
          onClick={(e) => {
            e.stopPropagation();
          }}
        >
          <Heart className="w-4 h-4 text-muted-foreground hover:text-destructive transition-colors" />
        </button>
        {discount > 0 && (
          <span className="absolute top-2 left-2 bg-success text-success-foreground text-xs font-semibold px-2 py-0.5 rounded">
            {discount}% OFF
          </span>
        )}
      </div>
      <div className="p-3">
        <h3 className="text-sm text-foreground line-clamp-2 mb-2 leading-tight">
          {title}
        </h3>
        <div className="space-y-0.5">
          {originalPrice && (
            <span className="text-discount">
              {formatPrice(originalPrice)}
            </span>
          )}
          <p className="text-price text-lg">
            {formatPrice(price)}
          </p>
          {freeShipping && (
            <p className="badge-free-shipping">Frete grátis</p>
          )}
        </div>
      </div>
    </div>
  );
};

export default ProductCard;
