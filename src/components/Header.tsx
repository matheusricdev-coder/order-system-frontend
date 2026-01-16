import { ShoppingCart, User, Menu } from "lucide-react";
import SearchBar from "./SearchBar";
import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";

const Header = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const navigate = useNavigate();

  return (
    <header className="sticky top-0 z-50 bg-brand shadow-sm">
      <div className="container py-3">
        <div className="flex items-center gap-3">
          <button className="lg:hidden p-2 -ml-2">
            <Menu className="w-6 h-6 text-primary-foreground" />
          </button>
          
          <Link to="/" className="flex-shrink-0">
            <h1 className="text-xl font-bold text-primary-foreground tracking-tight">
              MarketPlace
            </h1>
          </Link>

          <div className="flex-1 hidden sm:block">
            <SearchBar 
              value={searchQuery}
              onChange={setSearchQuery}
              onSearch={() => console.log("Searching:", searchQuery)}
            />
          </div>

          <div className="flex items-center gap-1">
            <button 
              onClick={() => navigate("/login")}
              className="p-2 hover:bg-brand-hover rounded-lg transition-colors"
            >
              <User className="w-6 h-6 text-primary-foreground" />
            </button>
            <button className="p-2 hover:bg-brand-hover rounded-lg transition-colors relative">
              <ShoppingCart className="w-6 h-6 text-primary-foreground" />
              <span className="absolute -top-0.5 -right-0.5 bg-destructive text-destructive-foreground text-xs font-bold w-5 h-5 flex items-center justify-center rounded-full">
                3
              </span>
            </button>
          </div>
        </div>

        <div className="mt-3 sm:hidden">
          <SearchBar 
            value={searchQuery}
            onChange={setSearchQuery}
            onSearch={() => console.log("Searching:", searchQuery)}
          />
        </div>
      </div>
    </header>
  );
};

export default Header;
